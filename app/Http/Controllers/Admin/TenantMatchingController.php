<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\RentalNeed;
use App\Support\TenantAccess;
use Illuminate\Http\Request;

class TenantMatchingController extends Controller
{
    // Approved homes that fit the need: the area, rent within budget, enough bedrooms
    private function suggestions(RentalNeed $need)
    {
        $tokens = collect(preg_split('/[,;\/]+/', (string) $need->areas))
            ->map(fn ($t) => mb_strtolower(trim($t)))->filter()->values();
        $already = PropertyMatch::where('rental_need_id', $need->id)->where('status', 'Offered')->pluck('property_id')->all();

        return Property::where('status', 'Approved')
            ->where('rent', '<=', $need->budget_max)
            ->when($already, fn ($q) => $q->whereNotIn('id', $already))
            ->get()
            ->filter(function ($p) use ($need, $tokens) {
                if ((int) $p->bedrooms < $need->minBedrooms()) {
                    return false;
                }
                $haystack = mb_strtolower($p->suburb . ' ' . $p->city);
                return $tokens->isEmpty() || $tokens->contains(fn ($t) => str_contains($haystack, $t));
            })
            ->values();
    }

    public function index()
    {
        $open = RentalNeed::where('status', 'Open')->with('user')->latest()->get()
            ->each(function ($need) {
                $need->paid = TenantAccess::hasPaidMatchingFee($need->user_id);
                $need->suggested = $this->suggestions($need);
            });

        $matched = RentalNeed::where('status', 'Matched')->with(['user', 'matches.property'])->latest()->take(30)->get()
            ->each(fn ($need) => $need->paid = TenantAccess::hasPaidMatchingFee($need->user_id));

        return view('admin.tenant-matching', ['open' => $open, 'matched' => $matched]);
    }

    public function send(Request $request, RentalNeed $need)
    {
        $data = $request->validate([
            'property_ids'   => 'required|array|min:1',
            'property_ids.*' => 'integer|exists:properties,id',
        ]);

        $count = 0;
        foreach (Property::whereIn('id', $data['property_ids'])->where('status', 'Approved')->get() as $property) {
            PropertyMatch::updateOrCreate(
                ['rental_need_id' => $need->id, 'property_id' => $property->id],
                ['user_id' => $need->user_id, 'matched_by' => auth()->id(), 'status' => 'Offered']
            );
            $count++;
        }
        if ($count === 0) {
            return back()->with('error', 'None of those places are live, so nothing was sent.');
        }
        $need->update(['status' => 'Matched']);

        return back()->with('success', 'Sent ' . $count . ' ' . \Illuminate\Support\Str::plural('match', $count) . ' to ' . optional($need->user)->name . '.');
    }

    public function unmatch(PropertyMatch $propertyMatch)
    {
        $propertyMatch->update(['status' => 'Declined']);

        $need = $propertyMatch->need;
        if ($need && !$need->matches()->where('status', 'Offered')->exists()) {
            $need->update(['status' => 'Open']); // nothing left offered, so it needs matching again
        }

        return back()->with('success', 'Match removed.');
    }
}
