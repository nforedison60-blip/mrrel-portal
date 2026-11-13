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
use App\Models\ProviderLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PaymentController;

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role == 'Admin') return redirect()->route('admin.dashboard');
    if ($role == 'Provider') return redirect()->route('provider.dashboard');
    if ($role == 'Landlord') return redirect()->route('landlord.properties');
    return redirect()->route('customer.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    
    // PAYMENT ROUTES
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');

    // CUSTOMER ROUTES
    Route::get('/', function () {
        return view('customer.dashboard', [
            'requestsCount' => ServiceRequest::count(),
            'quotesCount' => Quote::count(),
            'bookingsCount' => Booking::count(),
            'propertiesCount' => Property::where('status', 'Approved')->count(),
            'nextBooking' => Booking::latest()->first(),
            'latestRequests' => ServiceRequest::latest()->take(3)->get()
        ]);
    })->name('customer.dashboard');

    Route::get('/customer/search', function (Request $request) {
        $query = Property::where('status', 'Approved');
        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->where(function($q) use ($location) {
                $q->where('suburb', 'like', "%{$location}%")->orWhere('city', 'like', "%{$location}%");
            });
        }
        if ($request->filled('max_price')) {
            $query->where('rent', '<=', $request->input('max_price'));
        }
        return view('customer.search', ['properties' => $query->latest()->get()]);
    })->name('customer.search');

    Route::get('/customer/tracking', [TrackingController::class, 'customerMap'])->name('customer.tracking');
    Route::get('/customer/tracking/latest', [TrackingController::class, 'getLatestLocation'])->name('customer.tracking.latest');

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

    Route::post('/requests/{serviceRequest}/publish', function (ServiceRequest $serviceRequest) {
        $serviceRequest->update(['status' => 'Active']);
        return redirect()->route('customer.requests')->with('success', 'Request submitted! Providers will now send quotes.');
    })->name('customer.requests.publish');

    Route::post('/requests/draft', function (Request $request) {
        ServiceRequest::create([
            'services_needed' => $request->services_needed ?? 'Draft',
            'moving_from' => $request->moving_from ?? 'TBD',
            'moving_to' => $request->moving_to ?? 'TBD',
            'move_date' => $request->move_date ?? now(),
            'date_flexibility' => $request->date_flexibility ?? 'Exact date',
            'item_size' => $request->item_size ?? 'Small items',
            'access_pickup' => $request->access_pickup ?? 'Ground floor',
            'access_delivery' => $request->access_delivery ?? 'Ground floor',
            'status' => 'Draft',
        ]);
        return redirect()->route('customer.dashboard')->with('success', 'Draft saved!');
    })->name('customer.requests.draft');

    Route::get('/quotes', function () {
        $quotes = Quote::latest()->get();
        return view('customer.quotes', ['quotes' => $quotes]);
    })->name('customer.quotes');

    Route::get('/quote/{quote}', function (Quote $quote) {
        return view('customer.quote-detail', ['quote' => $quote]);
    })->name('customer.quote.show');

    Route::get('/bookings', function () {
        $booking = Booking::latest()->first();
        return view('customer.bookings', ['booking' => $booking]);
    })->name('customer.bookings');

    Route::post('/bookings', function (Request $request) {
        Booking::create([
            'quote_id' => $request->quote_id,
            'customer_name' => auth()->user()->name,
            'total_price' => $request->total_price,
            'status' => 'Paid',
            'tracking_status' => 'On my way',
        ]);
        return redirect()->route('customer.bookings')->with('success', 'Quote accepted! Booking created.');
    })->name('customer.bookings.store');

    Route::post('/bookings/{booking}/confirm', function (Booking $booking) {
        $booking->update(['job_confirmed' => true]);
        return redirect()->route('customer.bookings')->with('success', 'Job confirmed! Thank you.');
    })->name('customer.bookings.confirm');

    Route::post('/bookings/{booking}/extras/approve', function (Booking $booking) {
        $booking->update(['extras_status' => 'Approved']);
        return redirect()->route('customer.bookings')->with('success', 'Extra charge approved and paid.');
    })->name('customer.extras.approve');

    Route::post('/bookings/{booking}/extras/decline', function (Booking $booking) {
        $booking->update(['extras_status' => 'Declined']);
        return redirect()->route('customer.bookings')->with('success', 'Extra charge declined.');
    })->name('customer.extras.decline');

    Route::post('/review', function (Request $request) {
        Review::create([
            'provider_id' => 1,
            'customer_name' => auth()->user()->name,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return redirect()->route('customer.bookings')->with('success', 'Thank you for your review!');
    })->name('customer.review.store');

    Route::get('/shortlist', function () {
        $properties = Property::whereIn('status', ['Approved', 'Under Offer'])->latest()->get();
        return view('customer.shortlist', ['properties' => $properties]);
    })->name('customer.shortlist');

    Route::post('/shortlist/{property}/accept', function (Property $property) {
        $property->update(['status' => 'Under Offer']);
        return redirect()->route('customer.shortlist')->with('success', 'You accepted the property! MrRel will contact you to finalize the lease.');
    })->name('customer.shortlist.accept');

    Route::post('/shortlist/{property}/decline', function (Property $property) {
        return redirect()->route('customer.shortlist')->with('success', 'You declined the property.');
    })->name('customer.shortlist.decline');

    Route::post('/viewing-request', function (Request $request) {
        ViewingRequest::create([
            'property_id' => $request->property_id,
            'customer_name' => auth()->user()->name,
            'status' => 'Pending',
            'message' => $request->message,
        ]);
        return redirect()->route('customer.shortlist')->with('success', 'Viewing request sent to the landlord!');
    })->name('customer.viewing.store');

    Route::get('/customer/profile', function () {
        $customer = Customer::firstOrCreate(['id' => 1], ['name' => auth()->user()->name, 'email' => auth()->user()->email, 'phone' => '082 000 0000']);
        return view('customer.profile', ['customer' => $customer]);
    })->name('customer.profile');

    Route::post('/customer/profile', function (Request $request) {
        $customer = Customer::find(1);
        $customer->update(['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone]);
        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    })->name('customer.profile.update');

    Route::get('/customer/messages', function () {
        $messages = Message::latest()->get();
        return view('customer.messages', ['messages' => $messages]);
    })->name('customer.messages');

    Route::post('/customer/messages', function (Request $request) {
        Message::create(['sender_name' => auth()->user()->name, 'message' => $request->message]);
        return redirect()->route('customer.messages')->with('success', 'Message sent!');
    })->name('customer.messages.store');

    Route::get('/customer/fees', function () {
        $properties = Property::whereIn('status', ['Approved', 'Under Offer'])->get();
        return view('customer.fees', ['properties' => $properties]);
    })->name('customer.fees');

    Route::get('/customer/problems', function () {
        $problems = Problem::latest()->get();
        return view('customer.problems', ['problems' => $problems]);
    })->name('customer.problems');

    Route::post('/customer/problems', function (Request $request) {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('problems', 's3');
        }
        Problem::create([
            'customer_name' => auth()->user()->name,
            'booking_id' => $request->booking_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'image_path' => $imagePath,
        ]);
        return redirect()->route('customer.problems')->with('success', 'Problem reported successfully!');
    })->name('customer.problems.store');

    // LANDLORD ROUTES
    Route::get('/landlord/properties', function () {
        $properties = Property::latest()->get();
        return view('landlord.properties', ['properties' => $properties]);
    })->name('landlord.properties');

    Route::get('/landlord/add-property', function () {
        return view('landlord.add-property');
    })->name('landlord.add-property');

    Route::post('/landlord/add-property', function (Request $request) {
        $request->validate(['image' => 'nullable|image|max:5120']);
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

    Route::get('/landlord/viewing-requests', function () {
        $viewingRequests = ViewingRequest::latest()->get();
        return view('landlord.viewing-requests', ['viewingRequests' => $viewingRequests]);
    })->name('landlord.viewing-requests');

    Route::post('/landlord/viewing/{viewingRequest}/resolve', function (ViewingRequest $viewingRequest) {
        $viewingRequest->update(['status' => 'Completed']);
        return redirect()->route('landlord.viewing-requests')->with('success', 'Viewing request marked as completed!');
    })->name('landlord.viewing.resolve');

    Route::get('/landlord/profile', function () {
        return view('landlord.profile');
    })->name('landlord.profile');

    // PROVIDER ROUTES
    Route::get('/provider/dashboard', function () {
        $user = auth()->user();
        $provider = Provider::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'company_name' => null,
                'vehicle' => null,
                'plate_number' => null,
                'verification_status' => 'Pending',
                'rating' => 0,
            ]
        );
        
        return view('provider.dashboard', [
            'provider' => $provider,
            'quotesCount' => Quote::count(),
            'bookingsCount' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'nextJobs' => Booking::latest()->take(3)->get(),
            'requests' => ServiceRequest::where('status', 'Active')->latest()->take(5)->get()
        ]);
    })->name('provider.dashboard');

    Route::get('/provider/send-quote/{id}', function ($id) {
        $serviceRequest = ServiceRequest::findOrFail($id);
        return view('provider.send-quote', ['serviceRequest' => $serviceRequest]);
    })->name('provider.send');

    Route::post('/provider/send-quote', function (Request $request) {
        try {
            $included = $request->has('included') ? implode(', ', (array)$request->input('included')) : '';
            $notIncluded = $request->has('not_included') ? implode(', ', (array)$request->input('not_included')) : '';

            Quote::create([
                'provider_name' => auth()->user()->name,
                'total_price' => $request->total_price ?? 0,
                'price_type' => $request->price_type ?? 'Fixed',
                'included' => $included,
                'not_included' => $notIncluded,
                'validity' => $request->validity ?? '48 hours',
                'note' => $request->note,
                'service_request_id' => $request->service_request_id,
            ]);
            return redirect()->route('provider.dashboard')->with('success', 'Quote sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Database error: ' . $e->getMessage());
        }
    })->name('provider.send.store');

    Route::get('/provider/requests', function () {
        $requests = ServiceRequest::where('status', 'Active')->latest()->get();
        return view('provider.requests', ['requests' => $requests]);
    })->name('provider.requests');

    Route::get('/provider/quotes', function () {
        $quotes = Quote::latest()->get();
        $reviews = Review::latest()->get();
        $problems = Problem::latest()->get();
        return view('provider.quotes', [
            'quotes' => $quotes,
            'reviews' => $reviews,
            'problems' => $problems
        ]);
    })->name('provider.quotes');

    // THE PROVIDER REVIEWS ROUTE
    Route::get('/provider/reviews', function () {
        $reviews = Review::latest()->get();
        return view('provider.reviews', ['reviews' => $reviews]);
    })->name('provider.reviews');

    Route::get('/provider/subscription', function () {
        return view('provider.subscription');
    })->name('provider.subscription');

    Route::get('/provider/problems', function () {
        return view('provider.problems', [
            'problems' => Problem::latest()->get()
        ]);
    })->name('provider.problems');

    Route::get('/provider/bookings', function () {
        $bookings = Booking::latest()->get();
        $problems = Problem::latest()->get();
        return view('provider.pbook', ['bookings' => $bookings, 'problems' => $problems]);
    })->name('provider.pbook');

    Route::get('/provider/messages', function () {
        $messages = Message::latest()->get();
        return view('provider.messages', ['messages' => $messages]);
    })->name('provider.messages');

    Route::post('/provider/messages', function (Request $request) {
        Message::create(['sender_name' => auth()->user()->name, 'message' => $request->message]);
        return redirect()->route('provider.messages')->with('success', 'Message sent!');
    })->name('provider.messages.store');

    Route::post('/provider/booking/{booking}/status', function (Request $request, Booking $booking) {
        $booking->update(['tracking_status' => $request->status]);
        return redirect()->route('provider.dashboard')->with('success', 'Tracking status updated!');
    })->name('provider.booking.status');

    Route::get('/provider/rate-card', function () {
        return view('provider.rate');
    })->name('provider.rate');

    Route::get('/provider/profile', function () {
        $user = auth()->user();
        $provider = Provider::firstOrCreate(['email' => $user->email], ['name' => $user->name]);
        $reviews = Review::latest()->get();
        return view('provider.profile', ['provider' => $provider, 'reviews' => $reviews]);
    })->name('provider.profile');

    Route::post('/provider/profile', function (Request $request) {
        $request->validate(['image' => 'nullable|image|max:5120', 'vehicle_image' => 'nullable|image|max:5120', 'id_image' => 'nullable|image|max:5120']);
        $user = auth()->user();
        $provider = Provider::where('email', $user->email)->first();
        $imagePath = $provider->image_path;
        if ($request->hasFile('image')) $imagePath = $request->file('image')->store('providers', 's3');
        $vehicleImagePath = $provider->vehicle_image_path;
        if ($request->hasFile('vehicle_image')) $vehicleImagePath = $request->file('vehicle_image')->store('providers', 's3');
        $idImagePath = $provider->id_image_path;
        if ($request->hasFile('id_image')) $idImagePath = $request->file('id_image')->store('providers', 's3');
        $provider->update(['name' => $request->name, 'company_name' => $request->company_name, 'vehicle' => $request->vehicle, 'plate_number' => $request->plate_number, 'image_path' => $imagePath, 'vehicle_image_path' => $vehicleImagePath, 'id_image_path' => $idImagePath]);
        return redirect()->route('provider.profile')->with('success', 'Profile updated successfully!');
    })->name('provider.profile.update');

    // PROVIDER LIVE TRACKING ROUTES
    Route::get('/provider/tracking', [TrackingController::class, 'providerMap'])->name('provider.tracking');
    Route::post('/provider/tracking/update', [TrackingController::class, 'updateLocation'])->name('provider.tracking.update');

    // ADMIN ROUTES
    Route::get('/admin/dashboard', function (Request $request) {
        return view('admin.dashboard', [
            'providerApplications' => Provider::where('verification_status', 'Pending')->count(),
            'propertiesToReview' => Property::where('status', 'Draft')->count(),
            'openDisputes' => Problem::where('status', 'In progress')->count(),
        ]);
    })->name('admin.dashboard');

    Route::get('/admin/approvals', function () {
        $properties = Property::where('status', 'Draft')->latest()->get();
        $viewingRequests = ViewingRequest::latest()->get();
        $problems = Problem::latest()->get();
        return view('admin.approvals', ['properties' => $properties, 'viewingRequests' => $viewingRequests, 'problems' => $problems]);
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
            'providers' => Provider::latest()->get(),
            'requests' => ServiceRequest::latest()->get(),
            'quotes' => Quote::latest()->get()
        ]);
    })->name('admin.users');

    Route::get('/admin/requests', function () {
        return view('admin.requests', ['requests' => ServiceRequest::latest()->get(), 'quotes' => Quote::latest()->get()]);
    })->name('admin.requests');

    Route::get('/admin/bookings', function () {
        return view('admin.bookings', [
            'bookings' => Booking::with('quote')->latest()->get(),
            'properties' => Property::latest()->get(),
            'providers' => Provider::latest()->get()
        ]);
    })->name('admin.bookings');

    Route::get('/admin/messages', function () {
        return view('admin.messages', ['messages' => Message::latest()->get()]);
    })->name('admin.messages');

    Route::get('/admin/matching', function () {
        return view('admin.matching');
    })->name('admin.matching');

    Route::get('/admin/properties', function () {
        return view('admin.properties', ['properties' => Property::latest()->get()]);
    })->name('admin.properties');

    Route::get('/admin/payments', function () {
        $bookings = Booking::latest()->get();
        $totalRevenue = Booking::sum('total_price');
        return view('admin.payments', ['bookings' => $bookings, 'totalRevenue' => $totalRevenue, 'pendingPayouts' => $totalRevenue * 0.9, 'totalBookings' => $bookings->count()]);
    })->name('admin.payments');

    Route::get('/admin/disputes', function () {
        return view('admin.disputes', ['problems' => Problem::latest()->get()]);
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

    // ADMIN LIVE TRACKING ROUTE
    Route::get('/admin/map', [TrackingController::class, 'adminMap'])->name('admin.map');
});