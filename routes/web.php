<?php

use Illuminate\Support\Facades\Route;
use App\Models\ServiceRequest;
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
    return view('customer.quotes');
})->name('customer.quotes');

Route::get('/bookings', function () {
    return view('customer.bookings');
})->name('customer.bookings');

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

// NEW PROVIDER PAGES
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