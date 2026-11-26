<?php

namespace App\Support;

use App\Models\Property;

class TenantFees
{
    public static function matchingFee(): float
    {
        return (float) config('tenant.matching_fee', 99);
    }

    // The MrRel base the attendee travels from, or null when it has not been set (then the option is hidden)
    public static function baseLocation(): ?array
    {
        $lat = config('tenant.base.lat');
        $lng = config('tenant.base.lng');
        if ($lat === null || $lng === null || $lat === '' || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }
        return ['lat' => (float) $lat, 'lng' => (float) $lng, 'label' => config('tenant.base.label')];
    }

    public static function attendanceAvailable(): bool
    {
        return self::baseLocation() !== null;
    }

    // Straight-line distance x 2 (there and back) x road factor, rounded up. R150 covers the first 10 km, then R7.50 per km.
    public static function feeForOneWayKm(float $oneWayKm): array
    {
        $c = config('tenant.attendance');
        $roundTrip = (int) ceil(round($oneWayKm * 2 * $c['road_factor'], 4));
        $extra = max(0, $roundTrip - $c['included_km']);
        $fee = round($c['base_fee'] + $extra * $c['per_km'], 2);

        return ['round_trip_km' => $roundTrip, 'extra_km' => (int) $extra, 'fee' => $fee];
    }

    // The property's coordinates, looked up once from the suburb and city and then kept
    public static function propertyCoords(Property $property): ?array
    {
        if ($property->geo_lat !== null && $property->geo_lng !== null) {
            return ['lat' => (float) $property->geo_lat, 'lng' => (float) $property->geo_lng];
        }
        $place = trim(trim((string) $property->suburb) . ', ' . trim((string) $property->city), ', ');
        $found = Geo::geocode($place);
        if (!$found) {
            return null;
        }
        $property->forceFill(['geo_lat' => $found['lat'], 'geo_lng' => $found['lng']])->save();
        return $found;
    }

    // The fee for a viewing of this property, or null when it cannot be worked out
    public static function attendanceEstimate(Property $property): ?array
    {
        $base = self::baseLocation();
        if (!$base) {
            return null;
        }
        $coords = self::propertyCoords($property);
        if (!$coords) {
            return null;
        }
        $oneWay = Geo::distanceKm($base['lat'], $base['lng'], $coords['lat'], $coords['lng']);

        return self::feeForOneWayKm($oneWay) + ['one_way_km' => round($oneWay, 1)];
    }
}
