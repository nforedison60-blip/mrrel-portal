@extends('layouts.landlord')

@section('content')
    <h1>Add a property</h1>
    <div class="m">Saved as a draft until you submit it for MrRel’s review.</div>

    <form method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="sp" style="margin-top: 20px;">
            
            <!-- LEFT COLUMN: FORM -->
            <div class="card">
                <h3>Property details</h3>
                <div class="f">
                    <label>Title<input type="text" name="title" required></label>
                    <label>Description<input type="text" name="description"></label>
                    <label>Property type
                        <select name="property_type">
                            <option>Apartment</option>
                            <option>House</option>
                            <option>Townhouse</option>
                            <option>Room or cottage</option>
                        </select>
                    </label>
                    <label>City
                        <select name="city">
                            <option>Cape Town</option>
                            <option>Johannesburg</option>
                            <option>Pretoria</option>
                            <option>Durban</option>
                            <option>Gqeberha</option>
                            <option>Bloemfontein</option>
                            <option>Other</option>
                        </select>
                    </label>
                    <label>Suburb<input type="text" name="suburb" required></label>
                    <label>Street address or complex (private until a tenant is serious)<input type="text"></label>
                    <label>Bedrooms
                        <select name="bedrooms">
                            <option>Studio</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4+</option>
                        </select>
                    </label>
                    <label>Bathrooms
                        <select name="bathrooms">
                            <option>1</option>
                            <option>2</option>
                            <option>3+</option>
                        </select>
                    </label>
                    <label>Monthly rent (R)<input type="number" name="rent" required></label>
                    <label>Deposit (R)<input type="number" name="deposit" required></label>
                    <label>Minimum lease
                        <select>
                            <option>Month-to-month</option>
                            <option>6 months</option>
                            <option>12 months</option>
                            <option>Longer</option>
                        </select>
                    </label>
                    <label>Available from<input type="date"></label>
                    <label>Viewings possible
                        <select>
                            <option>Weekdays</option>
                            <option>Weekends</option>
                            <option>Evenings</option>
                            <option>By arrangement</option>
                        </select>
                    </label>
                    <label class="k">Features (parking, furnished, pets, secure complex, fibre, backup power)<input type="text"></label>
                    <label class="k">Extra costs and rules<input type="text"></label>
                    <label class="k">Tenant requirements<input type="text"></label>
                    <label class="k">Property photos (up to 10)<input type="file" name="image" accept="image/*"></label>
                    <label class="k" style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                        <input type="checkbox" id="own" required style="width: auto; margin: 0;"> I own this property or am authorised to let it
                    </label>
                </div>
                <button type="submit" class="btn">Submit for review</button>
                <button type="button" class="btn g">Save draft</button>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <!-- Photos Card -->
                <div class="card">
                    <h3>Photos</h3>
                    <div class="g3" style="margin-top: 12px; margin-bottom: 12px; gap: 8px;">
                        <div style="width: 100%; aspect-ratio: 1; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="40" height="40" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg>
                        </div>
                        <div style="width: 100%; aspect-ratio: 1; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="40" height="40" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg>
                        </div>
                        <div style="width: 100%; aspect-ratio: 1; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="40" height="40" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg>
                        </div>
                    </div>
                    <p class="m sm">Up to 10 photos. The first is the cover photo. JPG, PNG or WebP, up to 5 MB.</p>
                    <button type="button" class="btn g" style="margin-top: 12px;">Upload photos</button>
                </div>

                <!-- How it works Card -->
                <div class="card" style="margin-top: 16px;">
                    <h3>How it works</h3>
                    <ul class="sm">
                        <li>You submit the property</li>
                        <li>MrRel reviews it and may ask for changes</li>
                        <li>Approved properties are shared privately with matching tenants</li>
                        <li>You see viewing requests here</li>
                    </ul>
                </div>
            </div>

        </div>
    </form>
@endsection