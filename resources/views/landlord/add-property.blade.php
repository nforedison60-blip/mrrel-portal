@extends('layouts.landlord')

@section('content')
@php
    $types = ['Apartment', 'House', 'Townhouse', 'Room or cottage'];
    $cities = ['Cape Town', 'Johannesburg', 'Pretoria', 'Durban', 'Gqeberha', 'Bloemfontein', 'Other'];
    $beds = ['Studio', '1', '2', '3', '4+'];
    $baths = ['1', '2', '3+'];
@endphp

    <h1>Add a property</h1>
    <div class="m">MrRel reviews every property before tenants can see it.</div>

    <form method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="sp" style="margin-top: 20px;">

            <!-- LEFT COLUMN: FORM -->
            <div class="card">
                <h3>Property details</h3>
                <div class="f">
                    <label>Title<input type="text" name="title" value="{{ old('title') }}" maxlength="150" required></label>
                    <label>Description<input type="text" name="description" value="{{ old('description') }}" maxlength="1000" placeholder="Features, parking, pets, furnished, rules"></label>
                    <label>Property type
                        <select name="property_type">
                            @foreach($types as $option)
                                <option value="{{ $option }}" @selected(old('property_type') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>City
                        <select name="city">
                            @foreach($cities as $option)
                                <option value="{{ $option }}" @selected(old('city') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Suburb<input type="text" name="suburb" value="{{ old('suburb') }}" required></label>
                    <label>Bedrooms
                        <select name="bedrooms">
                            @foreach($beds as $option)
                                <option value="{{ $option }}" @selected(old('bedrooms') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Bathrooms
                        <select name="bathrooms">
                            @foreach($baths as $option)
                                <option value="{{ $option }}" @selected(old('bathrooms') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Monthly rent (R)<input type="number" name="rent" value="{{ old('rent') }}" min="0" step="1" required></label>
                    <label>Deposit (R)<input type="number" name="deposit" value="{{ old('deposit') }}" min="0" step="1" required></label>

                    <label class="k" style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                        <input type="checkbox" name="own" value="1" {{ old('own') ? 'checked' : '' }} required style="width: auto; margin: 0;"> I own this property or am authorised to let it
                    </label>
                </div>
                <button type="submit" class="btn">Submit for review</button>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <div class="card">
                    <h3>Photos</h3>
                    <p class="m sm">Up to 6 photos (JPG, PNG or WebP, up to 5 MB each). The first one is the cover photo.</p>

                    <div id="photoGrid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px;"></div>
                    <input type="file" name="photos[]" id="photosInput" accept="image/*" multiple style="display:none;">
                    <div id="fileName" class="m sm" style="margin-top: 8px;">No photos chosen yet.</div>

                    <button type="button" class="btn g" style="margin-top: 12px;" onclick="document.getElementById('photosInput').click()">Choose photos</button>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>How it works</h3>
                    <ul class="sm">
                        <li>You submit the property</li>
                        <li>MrRel reviews it and approves it</li>
                        <li>Approved properties are shown to tenants, who can ask for a viewing</li>
                        <li>You see viewing requests under Viewing requests</li>
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
            if (files.length > 6) {
                document.getElementById('fileName').textContent = 'Please choose at most 6 photos.';
                this.value = '';
                return;
            }
            document.getElementById('fileName').textContent = files.length ? files.length + (files.length === 1 ? ' photo chosen' : ' photos chosen') : 'No photos chosen yet.';
            files.forEach(function (file, i) {
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Selected photo ' + (i + 1);
                img.style.cssText = 'width:100%;height:80px;object-fit:cover;border-radius:8px;' + (i === 0 ? 'outline:3px solid #F4B731;' : '');
                grid.appendChild(img);
            });
        });
    </script>
@endsection
