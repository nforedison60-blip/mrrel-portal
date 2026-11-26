<?php

namespace App\Support;

use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\TenantPayment;
use App\Models\ViewingRequest;

/** Who may see full property details and ask for viewings. Decided on the server, never in the view alone. */
class TenantAccess
{
    // The once-off matching fee is paid once per tenant
    public static function hasPaidMatchingFee(int $userId): bool
    {
        return TenantPayment::where('user_id', $userId)
            ->where('type', TenantPayment::MATCHING_FEE)
            ->where('status', 'Paid')
            ->exists();
    }

    public static function isMatched(int $userId, int $propertyId): bool
    {
        return PropertyMatch::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->where('status', 'Offered')
            ->exists();
    }

    // Full details: fee paid AND the place is one of their matches (or they already accepted or asked to view it)
    public static function canSeeFull(int $userId, Property $property): bool
    {
        if (!self::hasPaidMatchingFee($userId)) {
            return false;
        }
        return self::isMatched($userId, $property->id)
            || (int) $property->accepted_by === $userId
            || ViewingRequest::where('user_id', $userId)->where('property_id', $property->id)->exists();
    }
}
