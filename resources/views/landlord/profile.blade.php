@extends('layouts.landlord')

@section('content')
    <h1>Profile</h1>
    <div class="m">Manage your agency details.</div>

    <div class="card" style="margin-top: 20px; max-width: 600px;">
        <form method="POST" action="#">
            @csrf
            <div class="f">
                <label>Owner or Agent Name
                    <input type="text" value="Sample Landlord" required>
                </label>
                <label>Agency Name (if applicable)
                    <input type="text" value="MrRel Properties">
                </label>
                <label class="k">PPRA Number (if applicable)
                    <input type="text" placeholder="e.g. 123456">
                </label>
            </div>
            <button type="button" class="btn">Save Details</button>
        </form>
    </div>
@endsection