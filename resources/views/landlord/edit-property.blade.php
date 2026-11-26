@extends('layouts.landlord')

@section('content')
@php
    // The saved value is always offered, even if it is not in the usual list
    $withSaved = fn($list, $saved) => collect($list)->push($saved)->filter()->unique()->values();
    $types = $withSaved(['Apartment', 'House', 'Townhouse', 'Room or cottage'], $property->property_type);
    $cities = $withSaved(['Cape Town', 'Johannesburg', 'Pretoria', 'Durban', 'Gqeberha', 'Bloemfontein', 'Other'], $property->city);
    $beds = $withSaved(['Studio', '1', '2', '3', '4+'], $property->bedrooms);
    $baths = $withSaved(['1', '2', '3+'], $property->bathrooms);

    // Every photo the property has: the cover first, then the others
    $photos = collect([$property->image_path])->merge($property->photo_paths ?? [])->filter()->values()->map(function ($path) {
        try { return ['path' => $path, 'url' => \Illuminate\Support\Facades\Storage::disk('s3')->url($path)]; } catch (\Throwable $e) { return null; }
    })->filter()->values();
@endphp

    <h1>Edit property</h1>
    <div class="m">
        @if($property->status === 'Approved')
            This property is live. After you save, MrRel reviews your changes and it goes live again once approved.
        @else
            Update your details. MrRel will see the changes when it reviews your property.
        @endif
    </div>

    <form method="POST" action="{{ route('landlord.properties.update', $property) }}" enctype="multipart/form-data">
        @csrf
        <div class="sp" style="margin-top: 20px;">

            <div class="card">
                <h3>Property details</h3>
                <div class="f">
                    <label>Title<input type="text" name="title" value="{{ old('title', $property->title) }}" maxlength="150" required></label>
                    <label>Description<input type="text" name="description" value="{{ old('description', $property->description) }}" maxlength="1000" placeholder="Features, parking, pets, furnished, rules"></label>
                    <label>Property type
                        <select name="property_type">
                            @foreach($types as $option)
                                <option value="{{ $option }}" @selected(old('property_type', $property->property_type) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>City
                        <select name="city">
                            @foreach($cities as $option)
                                <option value="{{ $option }}" @selected(old('city', $property->city) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Suburb<input type="text" name="suburb" value="{{ old('suburb', $property->suburb) }}" required></label>
                    <label>Bedrooms
                        <select name="bedrooms">
                            @foreach($beds as $option)
                                <option value="{{ $option }}" @selected(old('bedrooms', $property->bedrooms) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Bathrooms
                        <select name="bathrooms">
                            @foreach($baths as $option)
                                <option value="{{ $option }}" @selected(old('bathrooms', $property->bathrooms) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Monthly rent (R)<input type="number" name="rent" value="{{ old('rent', (int) $property->rent) }}" min="0" step="1" required></label>
                    <label>Deposit (R)<input type="number" name="deposit" value="{{ old('deposit', (int) $property->deposit) }}" min="0" step="1" required></label>
                </div>
                <button type="submit" class="btn">Save changes</button>
                <a href="{{ route('landlord.properties') }}" class="btn g" style="text-decoration:none;">Cancel</a>
            </div>

            <div>
                <div class="card">
                    <h3>Photos</h3>
                    <p class="m sm">Up to {{ $maxPhotos }} photos (JPG, PNG or WebP, up to 5 MB each). The first one is the cover photo.</p>

                    @if($photos->isNotEmpty())
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px;">
                            @foreach($photos as $i => $photo)
                                <label style="font-weight:500; font-size:12px; cursor:pointer;">
                                    <img src="{{ $photo['url'] }}" alt="Photo {{ $i + 1 }}" style="width:100%;height:80px;object-fit:cover;border-radius:8px;{{ $i === 0 ? 'outline:3px solid #F4B731;' : '' }}">
                                    <span style="display:block; margin-top:4px;"><input type="checkbox" name="remove_photos[]" value="{{ $photo['path'] }}"> Remove{{ $i === 0 ? ' (cover)' : '' }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="m sm" style="margin-top:12px;">No photos yet.</div>
                    @endif

                    <div id="photoGrid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px;"></div>
                    <input type="file" name="photos[]" id="photosInput" accept="image/*" multiple style="display:none;">
                    <div id="fileName" class="m sm" style="margin-top: 8px;">Tick Remove under a photo to delete it, or add more below. You have {{ $photos->count() }} of {{ $maxPhotos }}.</div>

                    <button type="button" class="btn g" style="margin-top: 12px;" onclick="document.getElementById('photosInput').click()">Add photos</button>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Good to know</h3>
                    <ul class="sm">
                        <li>A live property goes back to review when you save changes</li>
                        <li>Once a tenant accepts a property, it can no longer be edited here</li>
                    </ul>
                </div>
            </div>

        </div>
    </form>

    <script>
        document.getElementById('photosInput').addEventListener('change', function () {
            var files = Array.prototype.slice.call(this.files || []);
            var grid = document.getElementById('photoGrid');
            grid.innerHTML = '';
            document.getElementById('fileName').textContent = files.length ? files.length + (files.length === 1 ? ' new photo chosen' : ' new photos chosen') : 'No new photos chosen.';
            files.forEach(function (file, i) {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'New photo ' + (i + 1);
                img.style.cssText = 'width:100%;height:80px;object-fit:cover;border-radius:8px;';
                grid.appendChild(img);
            });
        });
    </script>
@endsection
