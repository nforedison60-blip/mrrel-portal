<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceRequest;
use App\Models\Quote;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;

// CUSTOMER ROUTES
Route::get('/', function () {
    $requests = ServiceRequest::latest()->get();
    return view('customer.dashboard', ['requests' => $requests]);
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

// UPDATED: Fetch approved properties for the shortlist
Route::get('/shortlist', function () {
    $properties = Property::where('status', 'Approved')->latest()->get();
    return view('customer.shortlist', ['properties' => $properties]);
})->name('customer.shortlist');

// LANDLORD ROUTES
Route::get('/landlord/properties', function () {
    $properties = Property::latest()->get();
    return view('landlord.properties', ['properties' => $properties]);
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

Route::get('/provider/bookings', function () {
    return view('provider.pbook');
})->name('provider.pbook');

Route::get('/provider/rate-card', function () {
    return view('provider.rate');
})->name('provider.rate');

// ADMIN ROUTES
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard', [
        'totalRequests' => ServiceRequest::count(),
        'totalQuotes' => Quote::count(),
        'totalBookings' => Booking::count(),
        'revenue' => Booking::sum('total_price'),
    ]);
})->name('admin.dashboard');

Route::get('/admin/approvals', function () {
    $properties = Property::where('status', 'Draft')->latest()->get();
    return view('admin.approvals', ['properties' => $properties]);
})->name('admin.approvals');

Route::post('/admin/approvals/{property}', function (Property $property) {
    $property->update(['status' => 'Approved']);
    return redirect()->route('admin.approvals')->with('success', 'Property approved successfully!');
})->name('admin.approvals.approve');

Route::get('/admin/map', function () {
    return view('admin.map');
})->name('admin.map');