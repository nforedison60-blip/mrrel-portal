@extends('layouts.landlord')

@section('content')
@php
    // The saved value is always offered, even if it is not in the usual list
    $withSaved = fn($list, $saved) => collect($list)->push($saved)->filter()->unique()->values();
    $types = $withSaved(['Apartment', 'House', 'Townhouse', 'Room or cottage'], $property->property_type);
    $cities = $withSaved(['Cape Town', 'Johannesburg', 'Pretoria', 'Durban', 'Gqeberha', 'Bloemfontein', 'Other'], $property->city);
    $beds = $withSaved(['Studio', '1', '2', '3', '4+'], $property->bedrooms);
    $baths = $withSaved(['1', '2', '3+'], $property->bathrooms);

    $photoUrl = null;
    if ($property->image_path) {
        try { $photoUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($property->image_path); } catch (\Throwable $e) { $photoUrl = null; }
    }
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
                    <h3>Photo</h3>
                    <p class="m sm">One cover photo. JPG, PNG or WebP, up to 5 MB. Leave it alone to keep the current photo.</p>

                    <img id="photoPreview" alt="Property photo" src="{{ $photoUrl }}" style="{{ $photoUrl ? '' : 'display:none;' }} width:100%; max-height:220px; object-fit:cover; border-radius:8px; margin-top:12px;">
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
                    <div id="fileName" class="m sm" style="margin-top: 8px;">{{ $property->image_path ? 'Current photo shown. Choose a new one to replace it.' : 'No photo yet.' }}</div>

                    <button type="button" class="btn g" style="margin-top: 12px;" onclick="document.getElementById('imageInput').click()">{{ $property->image_path ? 'Change photo' : 'Choose photo' }}</button>
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
        document.getElementById('imageInput').addEventListener('change', function () {
            var file = this.files[0];
            var preview = document.getElementById('photoPreview');
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });
    </script>
@endsection
