<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceRequest;
use App\Models\Quote;
use App\Models\Booking;
use App\Models\Property;
use App\Models\ViewingRequest;
use App\Models\Provider;
use Illuminate\Http\Request;

// CUSTOMER ROUTES
Route::get('/', function () {
    return view('customer.dashboard', [
        'requestsCount' => ServiceRequest::count(),
        'quotesCount' => Quote::count(),
        'bookingsCount' => Booking::count(),
        'propertiesCount' => Property::where('status', 'Approved')->count(),
        'latestRequests' => ServiceRequest::latest()->take(3)->get()
    ]);
})->name('customer.dashboard');

Route::get('/requests', function () {
    $requests = ServiceRequest::latest()->get();
    return view('customer.requests', ['requests' => $requests]); 
})->name('customer.requests');

Route::get('/requests/new', function () {
    return view('customer.new-request');
})->name('customer.new-request');

Route::post('/requests/new', function (Request $request) {
    ServiceRequest::create($request->all());
    return redirect()->route('customer.dashboard')->with('success', 'Request submitted successfully!');
})->name('customer.requests.store');

Route::get('/quotes', function () {
    $quotes = Quote::latest()->get();
    return view('customer.quotes', ['quotes' => $quotes]);
})->name('customer.quotes');

Route::get('/bookings', function () {
    $booking = Booking::latest()->first();
    return view('customer.bookings', ['booking' => $booking]);
})->name('customer.bookings');

Route::post('/bookings', function (Request $request) {
    Booking::create([
        'quote_id' => $request->quote_id,
        'customer_name' => 'Lerato',
        'total_price' => $request->total_price,
        'status' => 'Paid',
    ]);
    return redirect()->route('customer.bookings')->with('success', 'Quote accepted! Booking created.');
})->name('customer.bookings.store');

Route::get('/shortlist', function () {
    $properties = Property::where('status', 'Approved')->latest()->get();
    return view('customer.shortlist', ['properties' => $properties]);
})->name('customer.shortlist');

Route::post('/viewing-request', function (Request $request) {
    ViewingRequest::create([
        'property_id' => $request->property_id,
        'customer_name' => 'Lerato',
        'status' => 'Pending',
        'message' => $request->message,
    ]);
    return redirect()->route('customer.shortlist')->with('success', 'Viewing request sent to the landlord!');
})->name('customer.viewing.store');

// LANDLORD ROUTES
Route::get('/landlord/properties', function () {
    $properties = Property::latest()->get();
    $viewingRequests = ViewingRequest::latest()->get();
    return view('landlord.properties', [
        'properties' => $properties,
        'viewingRequests' => $viewingRequests
    ]);
})->name('landlord.properties');

Route::get('/landlord/add-property', function () {
    return view('landlord.add-property');
})->name('landlord.add-property');

Route::post('/landlord/add-property', function (Request $request) {
    Property::create([
        'title' => $request->title,
        'description' => $request->description,
        'property_type' => $request->property_type,
        'city' => $request->city,
        'suburb' => $request->suburb,
        'bedrooms' => $request->bedrooms,
        'bathrooms' => $request->bathrooms,
        'rent' => $request->rent,
        'deposit' => $request->deposit,
        'status' => 'Draft',
    ]);
    return redirect()->route('landlord.properties')->with('success', 'Property saved as draft!');
})->name('landlord.properties.store');

Route::post('/landlord/viewing/{viewingRequest}/resolve', function (ViewingRequest $viewingRequest) {
    $viewingRequest->update(['status' => 'Completed']);
    return redirect()->route('landlord.properties')->with('success', 'Viewing request marked as completed!');
})->name('landlord.viewing.resolve');

// PROVIDER ROUTES
Route::get('/provider/dashboard', function () {
    return view('provider.dashboard');
})->name('provider.dashboard');

Route::get('/provider/send-quote', function () {
    return view('provider.send-quote');
})->name('provider.send');

Route::post('/provider/send-quote', function (Request $request) {
    Quote::create([
        'provider_name' => 'Pieter', 
        'total_price' => $request->total_price,
        'price_type' => $request->price_type,
        'included' => implode(', ', $request->input('included', [])),
        'not_included' => implode(', ', $request->input('not_included', [])),
        'validity' => $request->validity,
        'note' => $request->note,
    ]);
    return redirect()->route('provider.dashboard')->with('success', 'Quote sent successfully!');
})->name('provider.send.store');

Route::get('/provider/requests', function () {
    return view('provider.requests');
})->name('provider.requests');

// UPDATED: Fetch real bookings for the Provider
Route::get('/provider/bookings', function () {
    $bookings = Booking::latest()->get();
    return view('provider.pbook', ['bookings' => $bookings]);
})->name('provider.pbook');

Route::get('/provider/rate-card', function () {
    return view('provider.rate');
})->name('provider.rate');

Route::get('/provider/profile', function () {
    $provider = Provider::firstOrCreate(
        ['id' => 1],
        [
            'name' => 'Pieter',
            'company_name' => 'Sample Movers B',
            'vehicle' => '1-ton bakkie',
            'plate_number' => 'CA 000 000',
            'verification_status' => 'Verified',
            'rating' => 4.8,
        ]
    );
    return view('provider.profile', ['provider' => $provider]);
})->name('provider.profile');

Route::post('/provider/profile', function (Request $request) {
    $provider = Provider::find(1);
    $provider->update([
        'name' => $request->name,
        'company_name' => $request->company_name,
        'vehicle' => $request->vehicle,
        'plate_number' => $request->plate_number,
    ]);
    return redirect()->route('provider.profile')->with('success', 'Profile updated successfully!');
})->name('provider.profile.update');

// ADMIN ROUTES
Route::get('/admin/dashboard', function (Request $request) {
    $status = $request->query('status', 'All');
    $search = $request->query('search');

    $query = Property::query();

    if ($status !== 'All') {
        $query->where('status', $status);
    }

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('suburb', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    return view('admin.dashboard', [
        'totalRequests' => ServiceRequest::count(),
        'totalQuotes' => Quote::count(),
        'totalBookings' => Booking::count(),
        'revenue' => Booking::sum('total_price'),
        'properties' => $query->latest()->get(),
    ]);
})->name('admin.dashboard');

Route::get('/admin/approvals', function () {
    $properties = Property::where('status', 'Draft')->latest()->get();
    $viewingRequests = ViewingRequest::latest()->get();
    return view('admin.approvals', [
        'properties' => $properties,
        'viewingRequests' => $viewingRequests
    ]);
})->name('admin.approvals');

Route::post('/admin/approvals/{property}', function (Property $property) {
    $property->update(['status' => 'Approved']);
    return redirect()->route('admin.approvals')->with('success', 'Property approved successfully!');
})->name('admin.approvals.approve');

Route::get('/admin/map', function () {
    return view('admin.map');
})->name('admin.map');