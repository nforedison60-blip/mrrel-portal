<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PropertyMatch;
use App\Models\RentalNeed;
use App\Models\ViewingRequest;
use App\Support\TenantAccess;
use App\Support\TenantFees;

/** Steps 2 and 3: the homes MrRel matched, shown as a preview until the once-off fee is paid. */
class TenantMatchController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $matches = PropertyMatch::where('user_id', $userId)
            ->where('status', 'Offered')
            ->with('property')
            ->latest()->get()
            ->filter(fn ($m) => $m->property !== null)
            ->values();

        return view('customer.matches', [
            'matches'  => $matches,
            'viewings' => ViewingRequest::where('user_id', $userId)->get()->keyBy('property_id'),
            'paid'     => TenantAccess::hasPaidMatchingFee($userId),
            'fee'      => TenantFees::matchingFee(),
            'need'     => RentalNeed::where('user_id', $userId)->whereIn('status', ['Open', 'Matched'])->latest()->first(),
        ]);
    }
}
