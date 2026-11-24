<?php

namespace App\Support;

use App\Models\Provider;
use App\Models\ServiceRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Location helpers: turns an address into coordinates and decides which providers can see which requests.
 * Address search uses OpenStreetMap's Nominatim service (free, South Africa only).
 */
class Geo
{
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    // "Claremont, Cape Town" -> ['lat' => -33.98, 'lng' => 18.46], or null if it cannot be found
    public static function geocode(string $address): ?array
    {
        $address = trim($address);
        if ($address === '') {
            return null;
        }

        $key = 'geo:' . md5(mb_strtolower($address));
        if ($hit = Cache::get($key)) {
            return $hit;
        }

        try {
            $response = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'MrRel/1.0 (' . (config('mail.from.address') ?: 'support@mrrel.co.za') . ')'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'za',
                ]);

            $first = $response->successful() ? ($response->json()[0] ?? null) : null;
            if (!$first || !isset($first['lat'], $first['lon'])) {
                return null;
            }
            $found = ['lat' => round((float) $first['lat'], 7), 'lng' => round((float) $first['lon'], 7)];
            Cache::put($key, $found, now()->addDays(30));
            return $found;
        } catch (\Throwable $e) {
            Log::warning('Geocoding failed: ' . $e->getMessage());
            return null;
        }
    }

    public static function hasLocation(?Provider $provider): bool
    {
        return $provider && $provider->base_lat !== null && $provider->base_lng !== null;
    }

    public static function radiusOf(Provider $provider): int
    {
        return max(1, (int) ($provider->service_radius_km ?: 50));
    }

    // Open requests whose pickup is inside this provider's service area, nearest first
    public static function openRequestsFor(?Provider $provider, array $excludeIds = []): Collection
    {
        if (!self::hasLocation($provider)) {
            return collect();
        }

        $lat = (float) $provider->base_lat;
        $lng = (float) $provider->base_lng;
        $radius = self::radiusOf($provider);
        $latDelta = $radius / 111.0;
        $lngDelta = $radius / (111.0 * max(cos(deg2rad($lat)), 0.1));

        return ServiceRequest::where('status', 'Active')
            ->whereNotNull('pickup_lat')->whereNotNull('pickup_lng')
            ->whereBetween('pickup_lat', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('pickup_lng', [$lng - $lngDelta, $lng + $lngDelta])
            ->when($excludeIds, fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->latest()->get()
            ->each(function ($r) use ($lat, $lng) {
                $r->distance_km = self::distanceKm($lat, $lng, (float) $r->pickup_lat, (float) $r->pickup_lng);
            })
            ->filter(fn ($r) => $r->distance_km <= $radius)
            ->values();
    }

    public static function requestInArea(?Provider $provider, ServiceRequest $request): bool
    {
        if (!self::hasLocation($provider) || $request->pickup_lat === null || $request->pickup_lng === null) {
            return false;
        }
        return self::distanceKm((float) $provider->base_lat, (float) $provider->base_lng, (float) $request->pickup_lat, (float) $request->pickup_lng)
            <= self::radiusOf($provider);
    }

    // Verified providers whose service area covers a point
    public static function providersCovering(float $lat, float $lng): Collection
    {
        return Provider::where('verification_status', 'Verified')
            ->whereNotNull('base_lat')->whereNotNull('base_lng')->get()
            ->filter(fn ($p) => self::distanceKm((float) $p->base_lat, (float) $p->base_lng, $lat, $lng) <= self::radiusOf($p))
            ->values();
    }
}
