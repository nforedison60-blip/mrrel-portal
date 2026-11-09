@extends('layouts.admin')

@section('content')
    <h1>Portal map</h1>
    <div class="m">What is in each portal. Each portal has its own login and sees only its own pages.</div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Customer or tenant portal</h3>
            <ul>
                <li>Dashboard: what needs attention</li>
                <li>My requests: create and track move or service requests</li>
                <li>Quotes: instant prices and quotes to compare, accept online</li>
                <li>Bookings: provider card, payment, extras, confirm completion, review</li>
                <li>My shortlist: private places: accept, decline, viewing, plan move</li>
                <li>Find a place: search for properties</li>
                <li>Messages: chat with providers</li>
                <li>Rental fees, Problems, Profile</li>
            </ul>
        </div>

        <div class="card">
            <h3>Landlord or agent portal</h3>
            <ul>
                <li>My properties: list with statuses</li>
                <li>Add property: details, photos, viewing times, submit for review</li>
                <li>Viewing requests: who asked and when, arranged by MrRel</li>
                <li>Profile: owner or agent, agency, PPRA number</li>
            </ul>
        </div>

        <div class="card">
            <h3>Provider portal</h3>
            <ul>
                <li>Dashboard: requests, jobs, payouts</li>
                <li>Requests: send quote in the MrRel template</li>
                <li>Bookings and payouts, Reviews</li>
                <li>Messages: chat with customers</li>
                <li>Rate card: prices for instant quotes</li>
                <li>Profile and verification, Subscription, Problems</li>
            </ul>
        </div>

        <div class="card">
            <h3>Admin</h3>
            <ul>
                <li>Dashboard: to-do and numbers</li>
                <li>Approvals: verify providers, approve properties</li>
                <li>Providers: view ID and vehicle photos for verification</li>
                <li>Users, Requests and quotes, Bookings</li>
                <li>Tenant matching: build private shortlists</li>
                <li>Payments and payouts, Disputes, Subscriptions</li>
                <li>Settings and reports</li>
            </ul>
        </div>
    </div>
    <p class="m sm">On a phone, the left menu becomes a bottom bar. Every screen is example data.</p>
@endsection