@extends('layouts.app')

@section('content')
@php
    $flexOptions = ['Exact date', 'Flexible by a few days', 'Flexible by a week or more'];
    $sizeOptions = ['Small items', 'Medium item', 'Large item', 'Extra large or special item', 'A few items', 'Studio or 1-bedroom', '2-bedroom home', '3-bedroom home', 'House', 'Not sure'];
    $accessOptions = ['Ground floor', 'Stairs', 'Lift', 'Not sure'];
@endphp

    <h1>New move or service request</h1>
    <div class="m">Providers in your area see this and send you quotes.</div>

    <div class="sp">
        <div class="card">
            <h3>Request details</h3>

            <form method="POST" action="{{ route('customer.requests.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="f">
                    <label class="k">Services needed (Packing, Movers, Cleaning, Storage, Assembly, Handyman, Painting, Gardening, Other)
                        <input type="text" name="services_needed" value="{{ old('services_needed') }}" required>
                    </label>
                    <label>Moving from or service address (city and suburb)
                        <input type="text" name="moving_from" id="movingFrom" value="{{ old('moving_from') }}" required>
                    </label>
                    <input type="hidden" name="pickup_lat" id="pickupLat" value="{{ old('pickup_lat') }}">
                    <input type="hidden" name="pickup_lng" id="pickupLng" value="{{ old('pickup_lng') }}">
                    <div class="k" style="margin: -4px 0 8px;">
                        <button type="button" class="btn g" id="useLocation" style="padding: 6px 14px; font-size: 13px;">Use my current location</button>
                        <span class="m sm" id="locationNote">Only movers near this address will see your request.</span>
                    </div>
                    <script>
                        (function () {
                            var btn = document.getElementById('useLocation'), note = document.getElementById('locationNote');
                            var lat = document.getElementById('pickupLat'), lng = document.getElementById('pickupLng'), addr = document.getElementById('movingFrom');
                            var normal = note.textContent;
                            btn.addEventListener('click', function () {
                                if (!navigator.geolocation) { note.textContent = 'Your browser cannot share its location. Type the address instead.'; return; }
                                note.textContent = 'Finding your location...';
                                navigator.geolocation.getCurrentPosition(function (pos) {
                                    lat.value = pos.coords.latitude.toFixed(7); lng.value = pos.coords.longitude.toFixed(7);
                                    note.textContent = 'Location captured. Movers near where you are now will see your request.';
                                }, function () {
                                    note.textContent = 'We could not get your location. Please type the address instead.';
                                }, { enableHighAccuracy: true, timeout: 10000 });
                            });
                            // Typing a different address cancels the captured location
                            addr.addEventListener('input', function () { lat.value = ''; lng.value = ''; note.textContent = normal; });
                        })();
                    </script>
                    <label>Moving to (if moving)
                        <input type="text" name="moving_to" value="{{ old('moving_to') }}">
                    </label>
                    <label>Move or service date
                        <input type="date" name="move_date" value="{{ old('move_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                    </label>
                    <label>Date flexibility
                        <select name="date_flexibility">
                            @foreach($flexOptions as $option)
                                <option value="{{ $option }}" @selected(old('date_flexibility') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>What are you moving?
                        <select name="item_size">
                            @foreach($sizeOptions as $option)
                                <option value="{{ $option }}" @selected(old('item_size') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>How many items or bags?
                        <input type="number" name="items_count" min="1" value="{{ old('items_count') }}">
                    </label>
                    <label>Budget in mind (optional)
                        <input type="text" name="budget" value="{{ old('budget') }}">
                    </label>
                    <label>Access at pickup
                        <select name="access_pickup">
                            @foreach($accessOptions as $option)
                                <option value="{{ $option }}" @selected(old('access_pickup') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Access at delivery
                        <select name="access_delivery">
                            @foreach($accessOptions as $option)
                                <option value="{{ $option }}" @selected(old('access_delivery') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="k">Photos of your items (optional, up to 5 images)
                        <input type="file" name="photos[]" id="photos" accept="image/jpeg,image/png,image/webp" multiple>
                    </label>
                    <div class="m sm" id="photosNote" style="margin:-4px 0 8px;">JPG, PNG or WebP, up to 5 MB each. Providers use them to quote more accurately.</div>
                    @foreach(collect($errors->get('photos'))->merge(collect($errors->get('photos.*'))->flatten())->unique() as $photoError)
                        <div class="m sm" style="color:#C0392B; margin:-4px 0 8px;">{{ $photoError }}</div>
                    @endforeach
                    <div id="photosPreview" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px;"></div>
                    <script>
                        (function () {
                            var input = document.getElementById('photos'), note = document.getElementById('photosNote'), box = document.getElementById('photosPreview');
                            var normal = note.textContent;
                            input.addEventListener('change', function () {
                                box.innerHTML = '';
                                var files = Array.prototype.slice.call(input.files);
                                var tooMany = files.length > 5;
                                var tooBig = files.some(function (f) { return f.size > 5 * 1024 * 1024; });
                                if (tooMany || tooBig) {
                                    note.textContent = tooMany ? 'Please choose at most 5 photos.' : 'Each photo must be smaller than 5 MB.';
                                    note.style.color = '#C0392B';
                                    input.value = '';
                                    return;
                                }
                                note.textContent = files.length ? files.length + (files.length === 1 ? ' photo selected.' : ' photos selected.') : normal;
                                note.style.color = '';
                                files.forEach(function (f) {
                                    var img = document.createElement('img');
                                    img.src = URL.createObjectURL(f);
                                    img.alt = 'Selected photo';
                                    img.style.cssText = 'width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #D5DEE6';
                                    box.appendChild(img);
                                });
                            });
                        })();
                    </script>
                    <label class="k">Details (large items, packing, anything else)
                        <input type="text" name="details" value="{{ old('details') }}">
                    </label>
                </div>
                <button type="submit" class="btn">Send to providers</button>
                <button type="submit" name="is_draft" value="1" class="btn g">Save draft</button>
                <p class="m sm" style="margin-top:10px;">A draft is saved but not shown to providers until you send it from My requests.</p>
            </form>
        </div>

        <div class="card">
            <h3>What happens next</h3>
            <ul>
                <li>Matching providers see your request</li>
                <li>They send you quotes, which appear under Quotes</li>
                <li>You compare them and accept the one you like</li>
                <li>You pay securely online to confirm your booking</li>
            </ul>
        </div>
    </div>
@endsection
