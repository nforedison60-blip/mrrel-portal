<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\ViewingRequest;
use App\Support\TenantAccess;
use App\Support\TenantFees;
use Illuminate\Http\Request;

/**
 * Steps 4 and 6. These two actions use the same web addresses as the older ones and are registered first
 * (see routes/tenant.php), so they take over. A tenant must have paid the matching fee, and the place
 * must be one of their matches.
 */
class TenantViewingController extends Controller
{
    // Returns an error message, or null when the tenant is allowed
    private function refusal(int $userId, Property $property): ?string
    {
        if (!TenantAccess::hasPaidMatchingFee($userId)) {
            return 'Please pay the once-off R' . number_format(TenantFees::matchingFee(), 0) . ' matching fee first. It unlocks the full details and viewings for the homes MrRel matched to you.';
        }
        if (!TenantAccess::isMatched($userId, $property->id)) {
            return 'You can arrange viewings for the homes MrRel has matched to you. Tell us what you need and we will find some.';
        }
        return null;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id'   => 'required|integer|exists:properties,id',
            'message'       => 'nullable|string|max:500',
            'mrrel_attends' => 'nullable|boolean',
        ]);
        $userId = auth()->id();
        $property = Property::findOrFail($data['property_id']);

        if ($message = $this->refusal($userId, $property)) {
            return redirect()->back()->with('error', $message);
        }
        if ($property->status !== 'Approved') {
            return redirect()->back()->with('error', 'Sorry, this place is no longer available.');
        }
        $already = ViewingRequest::where('user_id', $userId)
            ->where('property_id', $property->id)
            ->where('status', 'Pending')
            ->exists();
        if ($already) {
            return redirect()->route('customer.shortlist')->with('success', 'You already asked to view this place. MrRel is coordinating with the landlord.');
        }

        $match = PropertyMatch::where('user_id', $userId)->where('property_id', $property->id)->where('status', 'Offered')->first();
        $viewing = ViewingRequest::create([
            'property_id'   => $property->id,
            'user_id'       => $userId,
            'customer_name' => auth()->user()->name,
            'status'        => 'Pending',
            'message'       => $data['message'] ?? null,
        ]);
        $viewing->forceFill(['rental_need_id' => optional($match)->rental_need_id])->save();

        // Step 5 (optional): someone from MrRel attends. Only offered when MrRel's base location is set.
        if ($request->boolean('mrrel_attends') && TenantFees::attendanceAvailable()) {
            return redirect()->route('tenant.attendance.show', $viewing->id)
                ->with('success', 'Viewing request sent. Check the cost of having MrRel attend, or skip it.');
        }

        return redirect()->route('customer.shortlist')->with('success', 'Viewing request sent! MrRel will coordinate it with the landlord or authorised agent.');
    }

    public function accept(Property $property)
    {
        $userId = auth()->id();

        if ((int) $property->accepted_by === (int) $userId) {
            return redirect()->route('customer.shortlist')->with('success', 'You already accepted this place.');
        }
        if ($message = $this->refusal($userId, $property)) {
            return redirect()->back()->with('error', $message);
        }
        if ($property->status !== 'Approved') {
            return redirect()->back()->with('error', 'Sorry, this place is no longer available.');
        }
        $property->update(['status' => 'Under Offer', 'accepted_by' => $userId]);

        return redirect()->route('customer.shortlist')
            ->with('success', 'You accepted the place! You will finalise the lease directly with the landlord. Rent and deposit are paid to the landlord.');
    }
}
