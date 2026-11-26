<?php

namespace App\Support;

use App\Models\PropertyMatch;
use App\Models\ViewingRequest;

/** Rules for the landlord's tenant introductions. Decided on the server, never in the view alone. */
class LandlordIntro
{
    public static function fee(): float
    {
        return (float) config('introductions.fee', 250);
    }

    // Matches a landlord may see: their own properties, offered to a tenant who has paid the matching fee.
    // A place that is no longer live only stays visible when the introduction was already paid.
    public static function visibleFor(int $landlordId)
    {
        return PropertyMatch::where(fn ($q) => $q->where('status', 'Offered')->orWhere('intro_status', 'Paid'))
            ->where(fn ($q) => $q->whereNull('landlord_status')->orWhere('landlord_status', '!=', 'Declined'))
            ->whereHas('property', fn ($q) => $q->where('user_id', $landlordId))
            ->with(['property', 'need', 'user'])
            ->latest()
            ->get()
            ->filter(function ($m) {
                if (!TenantAccess::hasPaidMatchingFee((int) $m->user_id)) {
                    return false;
                }
                return $m->introPaid() || optional($m->property)->status === 'Approved';
            })
            ->values();
    }

    // A match this landlord may act on (own property, still offered, tenant committed). Otherwise null.
    public static function actionable(int $landlordId, int $matchId): ?PropertyMatch
    {
        $m = PropertyMatch::with('property')->find($matchId);
        if (!$m || $m->status !== 'Offered' || !$m->property || (int) $m->property->user_id !== $landlordId) {
            return null;
        }
        if (!TenantAccess::hasPaidMatchingFee((int) $m->user_id)) {
            return null;
        }
        return $m;
    }

    // Has the landlord paid the introduction for this tenant and property?
    public static function paidFor(int $tenantId, int $propertyId): bool
    {
        return PropertyMatch::where('user_id', $tenantId)->where('property_id', $propertyId)->where('intro_status', 'Paid')->exists();
    }

    // What the landlord sees as the tenant's name on a viewing request: the full name only once the introduction is paid
    public static function viewerName(ViewingRequest $req): string
    {
        $name = (string) $req->customer_name;
        if (!$req->user_id) {
            return $name;
        }
        $hasMatch = PropertyMatch::where('user_id', $req->user_id)->where('property_id', $req->property_id)->exists();
        if (!$hasMatch || self::paidFor((int) $req->user_id, (int) $req->property_id)) {
            return $name;
        }
        return self::firstName($name) . ' (full details after the introduction)';
    }

    public static function firstName(?string $name): string
    {
        $first = trim((string) preg_split('/\s+/', trim((string) $name))[0]);
        return $first !== '' ? $first : 'Tenant';
    }
}
