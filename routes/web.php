<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceRequest;
use App\Models\Quote;
use App\Models\Booking;
use App\Models\Property;
use App\Models\ViewingRequest;
use App\Models\Provider;
use App\Models\Customer;
use App\Models\Review;
use App\Models\Message;
use App\Models\Problem;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;

// Load Breeze Auth routes
require __DIR__.'/auth.php';

// SMART REDIRECT AFTER LOGIN
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    
    if ($role == 'Admin') return redirect()->route('admin.dashboard');
    if ($role == 'Provider') return redirect()->route('provider.dashboard');
    if ($role == 'Landlord') return redirect()->route('landlord.properties');
    
    return redirect()->route('customer.dashboard');
})->middleware(['auth'])->name('dashboard');

// BREEZE PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ALL PROTECTED ROUTES
Route::middleware(['auth'])->group(function () {
    
    // CUSTOMER ROUTES
    Route::get('/', function () {
        return view('customer.dashboard', [
            'requestsCount' => ServiceRequest::count(),
            'quotesCount' => Quote::count(),
            'bookingsCount' => Booking::count(),
            'propertiesCount' => Property::where('status', 'Approved')->count(),
            'nextBooking' => Booking::latest()->first(), // NEW
            'latestRequests' => ServiceRequest::latest()->take(3)->get()
        ]);
    })->name('customer.dashboard');

    Route::get('/customer/search', function (Request $request) {
        $query = Property::where('status', 'Approved');

        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function($q) use ($location) {
                $q->where('suburb', 'like', "%{$location}%")
                  ->orWhere('city', 'like', "%{$location}%");
            });
        }

        if ($request->filled('max_price')) {
            $query->where('rent', '<=', $request->input('max_price'));
        }

        return view('customer.search', ['properties' => $query->latest()->get()]);
    })->name('customer.search');

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
        $customer = Customer::find(1);
        Booking::create([
            'quote_id' => $request->quote_id,
            'customer_name' => $customer ? $customer->name : 'Lerato',
            'total_price' => $request->total_price,
            'status' => 'Paid',
        ]);
        return redirect()->route('customer.bookings')->with('success', 'Quote accepted! Booking created.');
    })->name('customer.bookings.store');

    Route::post('/review', function (Request $request) {
        $customer = Customer::find(1);
        Review::create([
            'provider_id' => 1,
            'customer_name' => $customer ? $customer->name : 'Lerato',
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return redirect()->route('customer.bookings')->with('success', 'Thank you for your review!');
    })->name('customer.review.store');

    Route::get('/shortlist', function () {
        $properties = Property::where('status', 'Approved')->latest()->get();
        return view('customer.shortlist', ['properties' => $properties]);
    })->name('customer.shortlist');

    Route::post('/viewing-request', function (Request $request) {
        $customer = Customer::find(1);
        ViewingRequest::create([
            'property_id' => $request->property_id,
            'customer_name' => $customer ? $customer->name : 'Lerato',
            'status' => 'Pending',
            'message' => $request->message,
        ]);
        return redirect()->route('customer.shortlist')->with('success', 'Viewing request sent to the landlord!');
    })->name('customer.viewing.store');

    Route::get('/customer/profile', function () {
        $customer = Customer::firstOrCreate(
            ['id' => 1],
            ['name' => 'Lerato', 'email' => 'lerato@example.com', 'phone' => '082 000 0000']
        );
        return view('customer.profile', ['customer' => $customer]);
    })->name('customer.profile');

    Route::post('/customer/profile', function (Request $request) {
        $customer = Customer::find(1);
        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    })->name('customer.profile.update');

    Route::get('/customer/messages', function () {
        $messages = Message::latest()->get();
        return view('customer.messages', ['messages' => $messages]);
    })->name('customer.messages');

    Route::post('/customer/messages', function (Request $request) {
        Message::create([
            'sender_name' => 'Lerato',
            'message' => $request->message,
        ]);
        return redirect()->route('customer.messages')->with('success', 'Message sent!');
    })->name('customer.messages.store');

    Route::get('/customer/fees', function () {
        return view('customer.fees');
    })->name('customer.fees');

    Route::get('/customer/problems', function () {
        $problems = Problem::latest()->get();
        return view('customer.problems', ['problems' => $problems]);
    })->name('customer.problems');

    Route::post('/customer/problems', function (Request $request) {
        $customer = Customer::find(1);
        Problem::create([
            'customer_name' => $customer ? $customer->name : 'Lerato',
            'description' => $request->description,
        ]);
        return redirect()->route('customer.problems')->with('success', 'Problem reported successfully!');
    })->name('customer.problems.store');

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
        $request->validate([
            'image' => 'nullable|image|max:5120', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('properties', 's3');
        }

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
            'image_path' => $imagePath,
        ]);
        return redirect()->route('landlord.properties')->with('success', 'Property saved as draft!');
    })->name('landlord.properties.store');

    Route::post('/landlord/viewing/{viewingRequest}/resolve', function (ViewingRequest $viewingRequest) {
        $viewingRequest->update(['status' => 'Completed']);
        return redirect()->route('landlord.properties')->with('success', 'Viewing request marked as completed!');
    })->name('landlord.viewing.resolve');

    // PROVIDER ROUTES
    Route::get('/provider/dashboard', function () {
        return view('provider.dashboard', [
            'quotesCount' => Quote::count(),
            'bookingsCount' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'nextJobs' => Booking::latest()->take(3)->get(),
            'requests' => ServiceRequest::latest()->take(5)->get()
        ]);
    })->name('provider.dashboard');

    Route::get('/provider/send-quote/{id}', function ($id) {
        $serviceRequest = ServiceRequest::findOrFail($id);
        return view('provider.send-quote', ['serviceRequest' => $serviceRequest]);
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
            'service_request_id' => $request->service_request_id,
        ]);
        return redirect()->route('provider.dashboard')->with('success', 'Quote sent successfully!');
    })->name('provider.send.store');

    Route::get('/provider/requests', function () {
        return view('provider.requests');
    })->name('provider.requests');

    Route::get('/provider/bookings', function () {
        $bookings = Booking::latest()->get();
        return view('provider.pbook', ['bookings' => $bookings]);
    })->name('provider.pbook');

    Route::get('/provider/messages', function () {
        $messages = Message::latest()->get();
        return view('provider.messages', ['messages' => $messages]);
    })->name('provider.messages');

    Route::post('/provider/messages', function (Request $request) {
        Message::create([
            'sender_name' => 'Pieter',
            'message' => $request->message,
        ]);
        return redirect()->route('provider.messages')->with('success', 'Message sent!');
    })->name('provider.messages.store');

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
        $reviews = Review::latest()->get();
        return view('provider.profile', [
            'provider' => $provider,
            'reviews' => $reviews
        ]);
    })->name('provider.profile');

    Route::post('/provider/profile', function (Request $request) {
        $request->validate([
            'image' => 'nullable|image|max:5120',
            'vehicle_image' => 'nullable|image|max:5120',
            'id_image' => 'nullable|image|max:5120',
        ]);

        $provider = Provider::find(1);
        
        $imagePath = $provider->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('providers', 's3');
        }

        $vehicleImagePath = $provider->vehicle_image_path;
        if ($request->hasFile('vehicle_image')) {
            $vehicleImagePath = $request->file('vehicle_image')->store('providers', 's3');
        }

        $idImagePath = $provider->id_image_path;
        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('providers', 's3');
        }

        $provider->update([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'vehicle' => $request->vehicle,
            'plate_number' => $request->plate_number,
            'image_path' => $imagePath,
            'vehicle_image_path' => $vehicleImagePath,
            'id_image_path' => $idImagePath,
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
        $problems = Problem::latest()->get();
        return view('admin.approvals', [
            'properties' => $properties,
            'viewingRequests' => $viewingRequests,
            'problems' => $problems
        ]);
    })->name('admin.approvals');

    Route::post('/admin/approvals/{property}', function (Property $property) {
        $property->update(['status' => 'Approved']);
        return redirect()->route('admin.approvals')->with('success', 'Property approved successfully!');
    })->name('admin.approvals.approve');

    Route::get('/admin/providers', function () {
        $providers = Provider::latest()->get();
        return view('admin.providers', ['providers' => $providers]);
    })->name('admin.providers');

    Route::post('/admin/providers/{provider}/verify', function (Provider $provider) {
        $provider->update(['verification_status' => 'Verified']);
        return redirect()->route('admin.providers')->with('success', 'Provider verified successfully!');
    })->name('admin.providers.verify');

    Route::get('/admin/users', function () {
        return view('admin.users', [
            'customers' => Customer::latest()->get(),
            'providers' => Provider::latest()->get()
        ]);
    })->name('admin.users');

    Route::get('/admin/requests', function () {
        return view('admin.requests', [
            'requests' => ServiceRequest::latest()->get(),
            'quotes' => Quote::latest()->get()
        ]);
    })->name('admin.requests');

    Route::get('/admin/bookings', function () {
        return view('admin.bookings', [
            'bookings' => Booking::with('quote')->latest()->get()
        ]);
    })->name('admin.bookings');

    Route::get('/admin/messages', function () {
        return view('admin.messages', [
            'messages' => Message::latest()->get()
        ]);
    })->name('admin.messages');

    Route::get('/admin/matching', function () {
        return view('admin.matching');
    })->name('admin.matching');

    Route::get('/admin/properties', function () {
        return view('admin.properties', [
            'properties' => Property::latest()->get()
        ]);
    })->name('admin.properties');

    Route::get('/admin/payments', function () {
        $bookings = Booking::latest()->get();
        $totalRevenue = Booking::sum('total_price');
        
        return view('admin.payments', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'pendingPayouts' => $totalRevenue * 0.9, 
            'totalBookings' => $bookings->count()
        ]);
    })->name('admin.payments');

    Route::get('/admin/disputes', function () {
        return view('admin.disputes', [
            'problems' => Problem::latest()->get()
        ]);
    })->name('admin.disputes');

    Route::post('/admin/disputes/{problem}/resolve', function (Problem $problem) {
        $problem->update(['status' => 'Resolved']);
        return redirect()->route('admin.disputes')->with('success', 'Dispute marked as resolved!');
    })->name('admin.disputes.resolve');

    Route::get('/admin/subscriptions', function () {
        return view('admin.subscriptions');
    })->name('admin.subscriptions');

    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');

    Route::get('/admin/map', function () {
        return view('admin.map');
    })->name('admin.map');
});