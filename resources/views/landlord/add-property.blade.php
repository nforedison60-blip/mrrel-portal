@extends('layouts.landlord')

@section('content')
    <h1>Add a property</h1>
    <div class="m">Saved as a draft until you submit it for MrRel’s review.</div>

    <form method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="sp">
            <div class="card">
                <h3>Property details</h3>
                <div class="f">
                    <label class="k">Title<input type="text" name="title" required></label>
                    <label class="k">Description<input type="text" name="description"></label>
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
                    <label class="k">Property Photo (Optional)<input type="file" name="image" accept="image/*"></label>
                </div>
                <button type="submit" class="btn">Submit for review</button>
            </div>

            <div class="card">
                <h3>How it works</h3>
                <ul>
                    <li>You submit the property</li>
                    <li>MrRel reviews it and may ask for changes</li>
                    <li>Approved properties are shared privately with matching tenants</li>
                </ul>
            </div>
        </div>
    </form>
@endsection