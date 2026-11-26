<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\RentalNeed;
use Illuminate\Http\Request;

/** Step 1: the tenant tells MrRel what they need. */
class TenantNeedController extends Controller
{
    private function rules(): array
    {
        return [
            'areas'        => 'required|string|max:255',
            'budget_min'   => 'nullable|integer|min:0|max:10000000',
            'budget_max'   => 'required|integer|min:1|max:10000000',
            'bedrooms'     => 'required|in:Studio,1,2,3,4,5+',
            'move_in_date' => 'nullable|date|after_or_equal:today',
            'notes'        => 'nullable|string|max:2000',
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate($this->rules());
        if (!empty($data['budget_min']) && (int) $data['budget_min'] > (int) $data['budget_max']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'budget_max' => 'The highest budget must be at least the lowest budget.',
            ]);
        }
        return $data;
    }

    private function openNeed(): ?RentalNeed
    {
        return RentalNeed::where('user_id', auth()->id())->where('status', 'Open')->latest()->first();
    }

    public function create()
    {
        // One open need per tenant: if there is one, they edit it
        if ($this->openNeed()) {
            return redirect()->route('customer.needs.edit');
        }
        return view('customer.needs', ['need' => null]);
    }

    public function store(Request $request)
    {
        if ($this->openNeed()) {
            return redirect()->route('customer.needs.edit')->with('error', 'You already have an open request. You can change it here.');
        }
        $data = $this->validated($request);

        // A new search closes an older one that was already matched
        RentalNeed::where('user_id', auth()->id())->where('status', 'Matched')->update(['status' => 'Closed']);
        RentalNeed::create($data + ['user_id' => auth()->id(), 'status' => 'Open']);

        return redirect()->route('customer.needs.edit')
            ->with('success', 'Thank you! MrRel will now look for homes that fit what you need.');
    }

    public function edit()
    {
        $need = $this->openNeed();
        if (!$need) {
            return redirect()->route('customer.needs.create');
        }
        return view('customer.needs', ['need' => $need]);
    }

    public function update(Request $request)
    {
        $need = $this->openNeed();
        if (!$need) {
            return redirect()->route('customer.needs.create');
        }
        $need->update($this->validated($request));

        return redirect()->route('customer.needs.edit')->with('success', 'Your request was updated.');
    }
}
