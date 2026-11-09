@extends('layouts.admin')

@section('content')
    <h1>Tenant Matching</h1>
    <div class="m">Build private shortlists for customers based on their requests.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Create a Match</h3>
        <div class="f">
            <label>Select Customer
                <select>
                    <option>Lerato (Looking for 2-bed in Cape Town)</option>
                </select>
            </label>
            <label>Select Property
                <select>
                    <option>NEW ESTATE, SUMMERGREENS</option>
                    <option>EDISON YUSUYUI NFOR, SUMMERGREENS</option>
                </select>
            </label>
        </div>
        <button type="button" class="btn">Send to Customer Shortlist</button>
    </div>
@endsection