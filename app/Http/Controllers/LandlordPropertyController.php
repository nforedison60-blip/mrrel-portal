<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Lets a landlord change a property they have listed, including its photos (up to 6: the first is the cover)
class LandlordPropertyController extends Controller
{
    public const MAX_PHOTOS = 6;

    public function edit(Property $property)
    {
        $this->own($property);
        if ($property->status === 'Under Offer') {
            return $this->locked();
        }

        return view('landlord.edit-property', ['property' => $property, 'maxPhotos' => self::MAX_PHOTOS]);
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
            'photos'        => 'nullable|array|max:' . self::MAX_PHOTOS,
            'photos.*'      => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_photos' => 'nullable|array',
            'remove_photos.*' => 'string|max:255',
        ], [
            'photos.max'        => 'You can add up to ' . self::MAX_PHOTOS . ' photos.',
            'photos.*.image'    => 'Each photo must be an image (JPG, PNG or WebP).',
            'photos.*.mimes'    => 'Each photo must be a JPG, PNG or WebP image.',
            'photos.*.max'      => 'Each photo must be smaller than 5 MB.',
            'photos.*.uploaded' => 'A photo could not be uploaded. Try smaller photos (under 5 MB each).',
        ]);
        unset($data['image'], $data['photos'], $data['remove_photos']);

        // What the property has now, what the landlord removes, and what they add
        $existing = collect([$property->image_path])->merge($property->photo_paths ?? [])->filter()->values();
        $remove = collect((array) $request->input('remove_photos', []))->intersect($existing)->values();
        $coverFile = $request->file('image');
        $newFiles = array_values(array_filter((array) $request->file('photos', [])));

        $oldCover = $property->image_path;
        $kept = $existing->reject(fn ($p) => $remove->contains($p) || ($coverFile && $p === $oldCover))->values();
        $total = $kept->count() + count($newFiles) + ($coverFile ? 1 : 0);
        if ($total > self::MAX_PHOTOS) {
            return back()->withInput()->withErrors(['photos' => 'A property can have up to ' . self::MAX_PHOTOS . ' photos. Remove some first, or add fewer.']);
        }

        // Save the new files first; if one fails, undo and keep everything as it was
        $stored = [];
        $coverPath = null;
        $ok = true;
        foreach (array_filter(array_merge([$coverFile], $newFiles)) as $file) {
            try { $path = $file->store('properties', 's3'); } catch (\Throwable $e) { $path = false; }
            if (!$path) { $ok = false; break; }
            $stored[] = $path;
            if ($coverFile && $file === $coverFile) { $coverPath = $path; }
        }
        if (!$ok) {
            $this->deleteFiles($stored);
            return back()->withInput()->withErrors(['photos' => 'We could not save the photos right now. Try again, or save without changing the photos.']);
        }

        // Order: new cover (if any), then the photos kept, then the new extras
        $final = collect($coverPath ? [$coverPath] : [])
            ->merge($kept)
            ->merge(collect($stored)->reject(fn ($p) => $p === $coverPath))
            ->values();
        $data['image_path'] = $final->first();
        $data['photo_paths'] = $final->slice(1)->values()->all() ?: null;

        $blocked = false;
        $wasLive = false;
        DB::transaction(function () use ($property, $data, &$blocked, &$wasLive) {
            $locked = Property::lockForUpdate()->find($property->id);
            if (! $locked || $locked->status === 'Under Offer') { // a tenant accepted while the landlord was typing
                $blocked = true;
                return;
            }
            $wasLive = $locked->status === 'Approved';
            // A live listing goes back to MrRel for a quick review, so tenants never see unchecked changes
            if ($wasLive) {
                $data['status'] = 'Draft';
            }
            $locked->update($data);
        });

        if ($blocked) {
            $this->deleteFiles($stored);
            return $this->locked();
        }
        // Photos that are no longer used
        $this->deleteFiles($existing->reject(fn ($p) => $final->contains($p))->all());

        return redirect()->route('landlord.properties')->with('success', $wasLive
            ? 'Changes saved. MrRel will review them, and the property goes live for tenants again once approved.'
            : 'Changes saved. MrRel will see the updated details when it reviews your property.');
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            try { Storage::disk('s3')->delete($path); } catch (\Throwable $e) {}
        }
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
