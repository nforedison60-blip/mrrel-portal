@extends('layouts.landlord')

@section('content')
    <h1>Profile</h1>
    <div class="m">Manage your agency details.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Profile</h3>
        <form method="POST" action="#">
            @csrf
            <div class="f">
                <label>Full name
                    <input type="text" value="Sample Landlord" required>
                </label>
                <label>Phone or WhatsApp
                    <input type="text" placeholder="e.g. 082 000 0000">
                </label>
                <label>Email
                    <input type="email" placeholder="you@example.com">
                </label>
                <label>You are the
                    <select>
                        <option>Owner</option>
                        <option>Agent</option>
                    </select>
                </label>
                <label>Agency or company
                    <input type="text" placeholder="e.g. MrRel Properties">
                </label>
                <label>PPRA registration number (agents)
                    <input type="text" placeholder="e.g. 123456">
                </label>
            </div>
            <button type="button" class="btn">Save profile</button>
        </form>
    </div>
@endsection