<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceRequest;
use App\Models\Quote;
use App\Models\Booking;
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

// UPDATED: Fetch real bookings
Route::get('/bookings', function () {
    $booking = Booking::latest()->first(); // Get the most recent booking for now
    return view('customer.bookings', ['booking' => $booking]);
})->name('customer.bookings');

// NEW: Save the booking when a customer accepts a quote
Route::post('/bookings', function (Request $request) {
    Booking::create([
        'quote_id' => $request->quote_id,
        'customer_name' => 'Lerato', // Hardcoded for now
        'total_price' => $request->total_price,
        'status' => 'Paid',
    ]);
    return redirect()->route('customer.bookings')->with('success', 'Quote accepted! Booking created.');
})->name('customer.bookings.store');

Route::get('/shortlist', function () {
    return view('customer.shortlist');
})->name('customer.shortlist');

// LANDLORD ROUTES
Route::get('/landlord/properties', function () {
    return view('landlord.properties');
})->name('landlord.properties');

Route::get('/landlord/add-property', function () {
    return view('landlord.add-property');
})->name('landlord.add-property');

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
Route::get('/admin/map', function () {
    return view('admin.map');
})->name('admin.map');