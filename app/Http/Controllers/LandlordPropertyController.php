<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Lets a landlord change a property they have listed
class LandlordPropertyController extends Controller
{
    public function edit(Property $property)
    {
        $this->own($property);
        if ($property->status === 'Under Offer') {
            return $this->locked();
        }

        return view('landlord.edit-property', ['property' => $property]);
    }

    public function update(Request $request, Property $property)
    {
        $this->own($property);
        if ($property->status === 'Under Offer') {
            return $this->locked();
        }

        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string|max:1000',
            'property_type' => 'required|string|max:50',
            'city'          => 'required|string|max:100',
            'suburb'        => 'required|string|max:100',
            'bedrooms'      => 'required|string|max:10',
            'bathrooms'     => 'required|string|max:10',
            'rent'          => 'required|numeric|min:0',
            'deposit'       => 'required|numeric|min:0',
            'image'         => 'nullable|image|max:5120',
        ]);
        unset($data['image']);

        // A new cover photo replaces the old one
        $newImage = null;
        if ($request->hasFile('image')) {
            try {
                $newImage = $request->file('image')->store('properties', 's3');
            } catch (\Throwable $e) {
                $newImage = false;
            }
            if (! $newImage) {
                return back()->withInput()->withErrors(['image' => 'We could not save the photo right now. Try again, or save without changing the photo.']);
            }
            $data['image_path'] = $newImage;
        }

        $blocked = false;
        $wasLive = false;
        $oldImage = null;
        DB::transaction(function () use ($property, $data, $newImage, &$blocked, &$wasLive, &$oldImage) {
            $locked = Property::lockForUpdate()->find($property->id);
            if (! $locked || $locked->status === 'Under Offer') { // a tenant accepted while the landlord was typing
                $blocked = true;
                return;
            }
            $wasLive = $locked->status === 'Approved';
            $oldImage = $newImage ? $locked->image_path : null;
            // A live listing goes back to MrRel for a quick review, so tenants never see unchecked changes
            if ($wasLive) {
                $data['status'] = 'Draft';
            }
            $locked->update($data);
        });

        if ($blocked) {
            if ($newImage) { try { Storage::disk('s3')->delete($newImage); } catch (\Throwable $e) {} }
            return $this->locked();
        }
        if ($oldImage) {
            try { Storage::disk('s3')->delete($oldImage); } catch (\Throwable $e) {}
        }

        return redirect()->route('landlord.properties')->with('success', $wasLive
            ? 'Changes saved. MrRel will review them, and the property goes live for tenants again once approved.'
            : 'Changes saved. MrRel will see the updated details when it reviews your property.');
    }

    private function own(Property $property): void
    {
        abort_unless((int) $property->user_id === (int) auth()->id(), 404);
    }

    private function locked()
    {
        return redirect()->route('landlord.properties')
            ->with('error', 'A tenant has accepted this property, so it can no longer be edited here. Please contact MrRel if something needs to change.');
    }
}
