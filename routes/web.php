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
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\NewQuoteReceived;
use App\Support\Geo;
use App\Support\Docs;

require __DIR__.'/auth.php';

// Ozow's server calls this: no login, no session, no CSRF token. The message is verified by its signature.
Route::post('/payment/notify', [PaymentController::class, 'notify'])->name('payment.notify');

// Opens a provider document through a short-lived link. Only the admin and the provider themselves may.
Route::get('/documents/{provider}/{field}', function (Provider $provider, string $field) {
    abort_unless(in_array($field, Docs::FIELDS, true), 404);
    $user = auth()->user();
    abort_unless($user->role === 'Admin' || (int) $provider->user_id === (int) $user->id, 403);

    $path = $provider->{$field};
    abort_unless($path, 404);

    if (!Docs::isPrivate($path)) { // an older file that was saved before the private bucket existed
        return redirect()->away(Storage::disk('s3')->url($path));
    }
    abort_unless(Docs::available(), 503);
    return redirect()->away(Docs::temporaryUrl($path, 5));
})->middleware('auth')->name('documents.show');

// Public home page. Logged-in people go straight to their own dashboard.
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('landing');
})->name('home');

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

    // CUSTOMER ROUTES
    Route::middleware('role:Customer')->group(function () {

    Route::get('/home', function () {
        $userId = auth()->id();
        return view('customer.dashboard', [
            'requestsCount' => ServiceRequest::where('user_id', $userId)->count(),
            'quotesCount' => Quote::whereHas('serviceRequest', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'bookingsCount' => Booking::where('user_id', $userId)->count(),
            'propertiesCount' => Property::where('status', 'Approved')->count(),
            'nextBooking' => Booking::where('user_id', $userId)->latest()->first(),
            'latestRequests' => ServiceRequest::where('user_id', $userId)->latest()->take(3)->get()
        ]);
    })->name('customer.dashboard');

    Route::get('/customer/search', function (Request $request) {
        $query = Property::where('status', 'Approved');
        if ($request->filled('location')) {
            // Case-insensitive on every database (LIKE is case-sensitive on PostgreSQL)
            $location = '%' . mb_strtolower(trim($request->input('location'))) . '%';
            $query->where(function($q) use ($location) {
                $q->whereRaw('LOWER(suburb) LIKE ?', [$location])->orWhereRaw('LOWER(city) LIKE ?', [$location]);
            });
        }
        if ($request->filled('max_price')) {
            $query->where('rent', '<=', $request->input('max_price'));
        }
        if ($request->filled('bedrooms')) {
            $query->whereRaw('CAST(bedrooms AS DECIMAL(10,0)) >= ?', [(int) $request->input('bedrooms')]);
        }
        // Which places this customer already asked to view
        $viewings = ViewingRequest::where('user_id', auth()->id())->get()->keyBy('property_id');
        return view('customer.search', ['properties' => $query->latest()->get(), 'viewings' => $viewings]);
    })->name('customer.search');

    Route::get('/customer/tracking', [TrackingController::class, 'customerMap'])->name('customer.tracking');
    Route::get('/customer/tracking/latest', [TrackingController::class, 'getLatestLocation'])->name('customer.tracking.latest');

    Route::get('/requests', function () {
        $requests = ServiceRequest::where('user_id', auth()->id())->latest()->get();
        return view('customer.requests', ['requests' => $requests]); 
    })->name('customer.requests');

    Route::get('/requests/new', function () {
        return view('customer.new-request');
    })->name('customer.new-request');

    Route::post('/requests/new', function (Request $request) {
        $data = $request->validate([
            'services_needed'  => 'required|string|max:255',
            'moving_from'      => 'required|string|max:255',
            'moving_to'        => 'nullable|string|max:255',
            'move_date'        => 'required|date|after_or_equal:today',
            'date_flexibility' => 'required|string|max:100',
            'item_size'        => 'required|string|max:100',
            'items_count'      => 'nullable|integer|min:1|max:1000',
            'budget'           => 'nullable|string|max:100',
            'access_pickup'    => 'required|string|max:100',
            'access_delivery'  => 'required|string|max:100',
            'details'          => 'nullable|string|max:2000',
            'pickup_lat'       => 'nullable|numeric|between:-36,-21',
            'pickup_lng'       => 'nullable|numeric|between:15,34',
        ]);
        $request->validate([
            'photos'   => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'photos.max'        => 'You can attach up to 5 photos.',
            'photos.*.image'    => 'Each photo must be an image (JPG, PNG or WebP).',
            'photos.*.mimes'    => 'Each photo must be a JPG, PNG or WebP image.',
            'photos.*.max'      => 'Each photo must be smaller than 5 MB.',
            'photos.*.uploaded' => 'A photo could not be uploaded. Try smaller photos (under 5 MB each).',
        ]);
        $isDraft = $request->has('is_draft');

        // Where the move starts: the phone's location if the customer used the button, otherwise the typed address
        $coords = ($request->filled('pickup_lat') && $request->filled('pickup_lng'))
            ? ['lat' => (float) $data['pickup_lat'], 'lng' => (float) $data['pickup_lng']]
            : Geo::geocode($data['moving_from']);
        if (!$coords && !$isDraft) {
            return back()->withInput()->withErrors(['moving_from' => 'We could not find that address. Add the suburb and city (for example Claremont, Cape Town) or use the location button.']);
        }
        $data['pickup_lat'] = $coords['lat'] ?? null;
        $data['pickup_lng'] = $coords['lng'] ?? null;

        $data['customer_name'] = auth()->user()->name;
        $data['user_id'] = auth()->id();
        $data['status'] = $isDraft ? 'Draft' : 'Active';
        // Photos of the items (optional, up to 5)
        $photoPaths = [];
        foreach ((array) $request->file('photos', []) as $photo) {
            try { $stored = $photo->store('service-requests', 's3'); } catch (\Throwable $e) { $stored = false; }
            if (!$stored) {
                if ($photoPaths) { \Illuminate\Support\Facades\Storage::disk('s3')->delete($photoPaths); }
                return back()->withInput()->withErrors(['photos' => 'We could not save your photos right now. Remove them and try again, or send the request without photos.']);
            }
            $photoPaths[] = $stored;
        }
        $data['photo_paths'] = $photoPaths ?: null;
        ServiceRequest::create($data);

        if ($isDraft) {
            return redirect()->route('customer.requests')->with('success', 'Draft saved. Send it to providers from here when you are ready.');
        }
        $near = Geo::providersCovering($coords['lat'], $coords['lng'])->count();
        return redirect()->route('customer.requests')->with('success', $near > 0
            ? 'Request sent! ' . $near . ' ' . \Illuminate\Support\Str::plural('mover', $near) . ' near you can now send quotes.'
            : 'Request sent. No verified movers cover your area yet, but your request stays open for when they do.');
    })->name('customer.requests.store');

    Route::post('/requests/{serviceRequest}/publish', function (ServiceRequest $serviceRequest) {
        abort_unless($serviceRequest->user_id === auth()->id(), 403);
        if ($serviceRequest->status === 'Draft') {
            if ($serviceRequest->pickup_lat === null) {
                $coords = Geo::geocode($serviceRequest->moving_from);
                if (!$coords) {
                    return redirect()->route('customer.requests')->with('error', 'We could not find the pickup address. Delete this draft and start again with the suburb and city.');
                }
                $serviceRequest->update(['pickup_lat' => $coords['lat'], 'pickup_lng' => $coords['lng']]);
            }
            $serviceRequest->update(['status' => 'Active']);
        }
        return redirect()->route('customer.requests')->with('success', 'Request sent! Movers near you can now send quotes.');
    })->name('customer.requests.publish');

    Route::post('/requests/{serviceRequest}/delete', function (ServiceRequest $serviceRequest) {
        abort_unless($serviceRequest->user_id === auth()->id(), 403);
        abort_unless($serviceRequest->status === 'Draft', 403);
        $serviceRequest->delete();
        return redirect()->route('customer.requests')->with('success', 'Draft deleted.');
    })->name('customer.requests.delete');

    // FILTERED QUOTES
    Route::get('/quotes', function () {
        $userId = auth()->id();
        $quotes = Quote::whereHas('serviceRequest', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->latest()->get();
        return view('customer.quotes', ['quotes' => $quotes]);
    })->name('customer.quotes');

    Route::get('/quote/{quote}', function (Quote $quote) {
        // A customer may only open quotes for their OWN requests
        abort_unless(optional($quote->serviceRequest)->user_id === auth()->id(), 403);
        return view('customer.quote-detail', ['quote' => $quote]);
    })->name('customer.quote.show');

    Route::get('/bookings', function (Request $request) {
        $all = Booking::where('user_id', auth()->id())->with('quote.serviceRequest')->latest()->get();
        $booking = $request->filled('booking') ? $all->firstWhere('id', (int) $request->booking) : $all->first();
        abort_if($request->filled('booking') && !$booking, 404);
        return view('customer.bookings', ['booking' => $booking, 'allBookings' => $all]);
    })->name('customer.bookings');

    Route::post('/bookings', function (Request $request) {
        $request->validate(['quote_id' => 'required|integer']);

        // Only allow quotes that belong to this customer's own requests
        $quote = Quote::whereHas('serviceRequest', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($request->quote_id);

        // One booking per request: stop a second booking for the same request
        $alreadyBooked = Booking::whereHas('quote', function ($q) use ($quote) {
            $q->where('service_request_id', $quote->service_request_id);
        })->exists();
        if ($alreadyBooked) {
            return redirect()->route('customer.bookings')->with('error', 'You have already booked a provider for this request.');
        }

        // A new booking is NOT paid and NOT started until that really happens
        $booking = Booking::create([
            'quote_id' => $quote->id,
            'user_id' => auth()->id(),
            'customer_name' => auth()->user()->name,
            'total_price' => $quote->total_price, // price comes from the quote, not from the form
            'status' => 'Awaiting payment',
            'tracking_status' => 'Not started',
        ]);
        return redirect()->route('payment.checkout', ['booking' => $booking->id])
            ->with('success', 'Quote accepted. Complete your payment to confirm the booking.');
    })->name('customer.bookings.store');

    Route::post('/bookings/{booking}/cancel', function (Booking $booking) {
        abort_unless($booking->user_id === auth()->id(), 403);
        if ($booking->status === 'Paid') {
            return redirect()->route('customer.bookings', ['booking' => $booking->id])
                ->with('error', 'A paid booking cannot be cancelled here. Please report a problem so MrRel can help.');
        }
        $booking->delete();
        return redirect()->route('customer.quotes')->with('success', 'Booking cancelled. You can choose another quote.');
    })->name('customer.bookings.cancel');

    Route::post('/bookings/{booking}/confirm', function (Booking $booking) {
        abort_unless($booking->user_id === auth()->id(), 403);
        if ($booking->status !== 'Paid') {
            return redirect()->route('customer.bookings', ['booking' => $booking->id])
                ->with('error', 'You can confirm the job once the booking has been paid.');
        }
        $booking->update(['job_confirmed' => true]);
        return redirect()->route('customer.bookings', ['booking' => $booking->id])->with('success', 'Job confirmed! Thank you.');
    })->name('customer.bookings.confirm');

    Route::post('/bookings/{booking}/extras/approve', function (Booking $booking) {
        abort_unless($booking->user_id === auth()->id(), 403);
        if ($booking->extras_status === 'Proposed') {
            $booking->update(['extras_status' => 'Approved']);
        }
        return redirect()->route('customer.bookings', ['booking' => $booking->id])->with('success', 'Extra charge approved.');
    })->name('customer.extras.approve');

    Route::post('/bookings/{booking}/extras/decline', function (Booking $booking) {
        abort_unless($booking->user_id === auth()->id(), 403);
        if ($booking->extras_status === 'Proposed') {
            $booking->update(['extras_status' => 'Declined']);
        }
        return redirect()->route('customer.bookings', ['booking' => $booking->id])->with('success', 'Extra charge declined.');
    })->name('customer.extras.decline');

    Route::post('/review', function (Request $request) {
        $data = $request->validate([
            'booking_id' => 'required|integer',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        // The review is for the provider of THIS customer's own booking
        $booking = Booking::where('user_id', auth()->id())->with('quote')->findOrFail($data['booking_id']);
        $back = redirect()->route('customer.bookings', ['booking' => $booking->id]);

        if (!$booking->job_confirmed) {
            return $back->with('error', 'You can review your provider after you confirm the job is done.');
        }
        $providerUserId = optional($booking->quote)->provider_user_id;
        if (Review::where('user_id', auth()->id())->where('provider_user_id', $providerUserId)->exists()) {
            return $back->with('error', 'You have already reviewed this provider.');
        }

        Review::create([
            'provider_user_id' => $providerUserId,
            'user_id' => auth()->id(),
            'customer_name' => auth()->user()->name,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);
        return $back->with('success', 'Thank you for your review!');
    })->name('customer.review.store');

    Route::get('/shortlist', function () {
        // Only places THIS customer showed interest in: accepted, or a viewing requested
        $userId = auth()->id();
        $viewings = ViewingRequest::where('user_id', $userId)->get()->keyBy('property_id');
        $properties = Property::where('accepted_by', $userId)
            ->orWhereIn('id', $viewings->keys())
            ->latest()->get();
        return view('customer.shortlist', ['properties' => $properties, 'viewings' => $viewings]);
    })->name('customer.shortlist');

    Route::post('/shortlist/{property}/accept', function (Property $property) {
        if ((int) $property->accepted_by === (int) auth()->id()) {
            return redirect()->route('customer.shortlist')->with('success', 'You already accepted this place.');
        }
        // Only a place that is still open can be accepted, and we remember who accepted it
        if ($property->status !== 'Approved') {
            return redirect()->back()->with('error', 'Sorry, this place is no longer available.');
        }
        $property->update(['status' => 'Under Offer', 'accepted_by' => auth()->id()]);
        return redirect()->route('customer.shortlist')->with('success', 'You accepted the place! MrRel will contact you to finalise the lease.');
    })->name('customer.shortlist.accept');

    Route::post('/viewing-request', function (Request $request) {
        $data = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'message'     => 'nullable|string|max:500',
        ]);
        $property = Property::findOrFail($data['property_id']);
        if ($property->status !== 'Approved') {
            return redirect()->back()->with('error', 'Sorry, this place is no longer available.');
        }
        $already = ViewingRequest::where('user_id', auth()->id())
            ->where('property_id', $property->id)
            ->where('status', 'Pending')
            ->exists();
        if ($already) {
            return redirect()->route('customer.shortlist')->with('success', 'You already asked to view this place. The landlord will respond soon.');
        }
        ViewingRequest::create([
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'customer_name' => auth()->user()->name,
            'status' => 'Pending',
            'message' => $data['message'] ?? null,
        ]);
        return redirect()->route('customer.shortlist')->with('success', 'Viewing request sent to the landlord!');
    })->name('customer.viewing.store');

    Route::get('/customer/profile', function () {
        // Each customer gets THEIR OWN profile row
        $customer = Customer::firstOrCreate(
            ['user_id' => auth()->id()],
            ['name' => auth()->user()->name, 'email' => auth()->user()->email, 'phone' => '']
        );
        return view('customer.profile', ['customer' => $customer]);
    })->name('customer.profile');

    Route::post('/customer/profile', function (Request $request) {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);
        $customer = Customer::where('user_id', auth()->id())->firstOrFail();
        $customer->update(['name' => $data['name'], 'phone' => $data['phone'] ?? '']);
        auth()->user()->update(['name' => $data['name']]);
        return redirect()->route('customer.profile')->with('success', 'Profile updated.');
    })->name('customer.profile.update');

    // The providers this customer has booked (the only people they can chat with)
    $myProviders = function () {
        return Booking::where('user_id', auth()->id())->with('quote')->latest()->get()
            ->map(function ($b) {
                return ['id' => optional($b->quote)->provider_user_id, 'name' => optional($b->quote)->provider_name];
            })
            ->filter(fn ($p) => !empty($p['id']))
            ->unique('id')
            ->values();
    };

    Route::get('/customer/messages', function (Request $request) use ($myProviders) {
        $userId = auth()->id();
        $providers = $myProviders();

        $first = $providers->first();
        $with = (int) $request->input('with', $first['id'] ?? 0);
        if (!$providers->contains('id', $with)) {
            $with = (int) ($first['id'] ?? 0);
        }

        $messages = collect();
        if ($with) {
            $messages = Message::where(function ($outer) use ($userId, $with) {
                $outer->where(function ($q) use ($userId, $with) {
                    $q->where('user_id', $userId)->where('receiver_id', $with);
                })->orWhere(function ($q) use ($userId, $with) {
                    $q->where('user_id', $with)->where('receiver_id', $userId);
                });
            })->oldest()->get();
        }

        $match = $providers->firstWhere('id', $with);

        return view('customer.messages', [
            'messages' => $messages,
            'providers' => $providers,
            'with' => $with,
            'withName' => $match['name'] ?? null,
        ]);
    })->name('customer.messages');

    Route::post('/customer/messages', function (Request $request) use ($myProviders) {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'with'    => 'required|integer',
        ]);
        if (!$myProviders()->contains('id', (int) $data['with'])) {
            return redirect()->route('customer.messages')->with('error', 'You can only message a provider you have booked.');
        }
        Message::create([
            'sender_name' => auth()->user()->name,
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'receiver_id' => (int) $data['with'],
        ]);
        return redirect()->route('customer.messages', ['with' => $data['with']]);
    })->name('customer.messages.store');

    Route::get('/customer/fees', function () {
        // Only the properties THIS customer accepted
        $properties = Property::where('accepted_by', auth()->id())->get();
        return view('customer.fees', ['properties' => $properties]);
    })->name('customer.fees');

    Route::get('/customer/problems', function () {
        $userId = auth()->id();
        return view('customer.problems', [
            'problems' => Problem::where('user_id', $userId)->latest()->get(),
            'bookings' => Booking::where('user_id', $userId)->with('quote')->latest()->get(),
        ]);
    })->name('customer.problems');

    Route::post('/customer/problems', function (Request $request) {
        $data = $request->validate([
            'booking_id'  => 'nullable|integer',
            'subject'     => 'required|string|max:150',
            'description' => 'required|string|max:2000',
            'image'       => 'nullable|image|max:5120',
        ]);

        // Only a booking that belongs to this customer can be attached
        $bookingId = null;
        if (!empty($data['booking_id'])) {
            $bookingId = Booking::where('user_id', auth()->id())->where('id', $data['booking_id'])->value('id');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('problems', 's3');
        }

        Problem::create([
            'customer_name' => auth()->user()->name,
            'booking_id' => $bookingId,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'image_path' => $imagePath,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('customer.problems')->with('success', 'Thank you. Your problem was sent and our team will look into it.');
    })->name('customer.problems.store');

    }); // end Customer

    // LANDLORD ROUTES
    Route::middleware('role:Landlord')->group(function () {

    Route::get('/landlord/properties', function () {
        $properties = Property::where('user_id', auth()->id())->latest()->get();
        return view('landlord.properties', ['properties' => $properties]);
    })->name('landlord.properties');

    Route::get('/landlord/add-property', function () {
        return view('landlord.add-property');
    })->name('landlord.add-property');

    Route::post('/landlord/add-property', function (Request $request) {
        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string|max:1000',
            'property_type' => 'required|string|max:50',
            'city'          => 'required|string|max:100',
            'suburb'        => 'required|string|max:100',
            'bedrooms'      => 'required|string|max:10',
            'bathrooms'     => 'required|string|max:10',
            'rent'          => 'required|numeric|min:0',
            'deposit'       => 'required|numeric|min:0',
            'own'           => 'accepted',
            'image'         => 'nullable|image|max:5120',
        ], [
            'own.accepted' => 'Please confirm that you own this property or are authorised to let it.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('properties', 's3');
        }

        // Status "Draft" means: sent to MrRel, waiting for review
        Property::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'property_type' => $data['property_type'],
            'city' => $data['city'],
            'suburb' => $data['suburb'],
            'bedrooms' => $data['bedrooms'],
            'bathrooms' => $data['bathrooms'],
            'rent' => $data['rent'],
            'deposit' => $data['deposit'],
            'status' => 'Draft',
            'image_path' => $imagePath,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('landlord.properties')->with('success', 'Property sent to MrRel for review. It goes live for tenants once MrRel approves it.');
    })->name('landlord.properties.store');

    Route::get('/landlord/viewing-requests', function () {
        // Only viewing requests for THIS landlord's properties
        $viewingRequests = ViewingRequest::whereHas('property', function ($q) {
            $q->where('user_id', auth()->id());
        })->latest()->get();
        return view('landlord.viewing-requests', ['viewingRequests' => $viewingRequests]);
    })->name('landlord.viewing-requests');

    Route::post('/landlord/viewing/{viewingRequest}/resolve', function (ViewingRequest $viewingRequest) {
        abort_unless(optional($viewingRequest->property)->user_id === auth()->id(), 403);
        $viewingRequest->update(['status' => 'Completed']);
        return redirect()->route('landlord.viewing-requests')->with('success', 'Viewing request marked as completed!');
    })->name('landlord.viewing.resolve');

    Route::get('/landlord/profile', function () {
        return view('landlord.profile', ['user' => auth()->user()]);
    })->name('landlord.profile');

    Route::post('/landlord/profile', function (Request $request) {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'landlord_type' => 'required|in:Owner,Agent',
            'agency_name'   => 'nullable|string|max:150',
            'ppra_number'   => 'nullable|string|max:50',
        ]);
        auth()->user()->update($data);
        return redirect()->route('landlord.profile')->with('success', 'Profile updated.');
    })->name('landlord.profile.update');

    }); // end Landlord

    // PROVIDER ROUTES
    Route::middleware('role:Provider')->group(function () {

    Route::get('/provider/dashboard', function () {
        $user = auth()->user();
        
        $provider = Provider::firstOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'company_name' => null,
                'vehicle' => null,
                'plate_number' => null,
                'verification_status' => 'Pending',
                'rating' => 0,
            ]
        );
        
        $nearby = Geo::openRequestsFor($provider, Quote::where('provider_user_id', $user->id)->pluck('service_request_id')->all());

        return view('provider.dashboard', [
            'provider' => $provider,
            'quotesCount' => Quote::where('provider_user_id', $user->id)->count(),
            'bookingsCount' => Booking::forProvider($user->id)->where('status', 'Paid')->count(),
            'totalRevenue' => Booking::forProvider($user->id)->where('status', 'Paid')->sum('total_price'),
            'nextJobs' => Booking::forProvider($user->id)->where('status', 'Paid')->with('quote.serviceRequest')->latest()->take(3)->get(),
            // Open requests inside this provider's service area that they have not quoted yet
            'requests' => $nearby->take(5),
            'openRequestsCount' => $nearby->count(),
            'needsLocation' => !Geo::hasLocation($provider),
            'commissionRate' => config('services.mrrel.commission_rate'),
            'paidOutTotal' => Booking::forProvider($user->id)->where('status', 'Paid')->whereNotNull('paid_out_at')->sum('total_price'),
        ]);
    })->name('provider.dashboard');

    // Helper: the Provider record that belongs to the logged-in user
    // Matches ignoring capital letters and spaces, and prefers a verified row if two exist
    $providerFor = function () {
        $user = auth()->user();
        // 1) the direct link between the login and its provider record
        $provider = Provider::where('user_id', $user->id)->first();
        if ($provider) {
            return $provider;
        }
        // 2) older records that only have an email: find them, then link them for good
        $provider = Provider::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])
            ->orderByRaw("CASE WHEN verification_status = 'Verified' THEN 0 ELSE 1 END")
            ->first();
        if ($provider && !$provider->user_id) {
            $provider->update(['user_id' => $user->id]);
        }
        return $provider;
    };
    $verifiedProvider = function () use ($providerFor) {
        $provider = $providerFor();
        return ($provider && $provider->verification_status === 'Verified') ? $provider : null;
    };
    $outsideAreaMessage = function () use ($providerFor) {
        return Geo::hasLocation($providerFor())
            ? 'That request is outside your service area. You can change your area under My business.'
            : 'Set your service area under My business first, so you only get requests near you.';
    };
    // Says exactly why quoting is blocked, so a mismatch is easy to spot
    $notVerifiedMessage = function () use ($providerFor) {
        $provider = $providerFor();
        $email = auth()->user()->email;
        if (!$provider) {
            return 'No provider profile was found for ' . $email . '. Open My business and save your details first.';
        }
        return 'Your account (' . $email . ') is not verified yet. Its status is "' . ($provider->verification_status ?: 'Pending') . '". Ask MrRel to verify this exact account.';
    };

    Route::get('/provider/send-quote/{serviceRequest}', function (ServiceRequest $serviceRequest) use ($verifiedProvider, $notVerifiedMessage, $outsideAreaMessage) {
        if (!$verifiedProvider()) {
            return redirect()->route('provider.profile')
                ->with('error', $notVerifiedMessage());
        }
        // Only open requests can be quoted (not drafts, not already handled)
        if ($serviceRequest->status !== 'Active') {
            return redirect()->route('provider.requests')
                ->with('error', 'This request is no longer open for quotes.');
        }
        if (!Geo::requestInArea($verifiedProvider(), $serviceRequest)) {
            return redirect()->route('provider.requests')->with('error', $outsideAreaMessage());
        }
        if (Quote::where('provider_user_id', auth()->id())->where('service_request_id', $serviceRequest->id)->exists()) {
            return redirect()->route('provider.quotes')
                ->with('error', 'You have already sent a quote for this request.');
        }
        return view('provider.send-quote', ['serviceRequest' => $serviceRequest]);
    })->whereNumber('serviceRequest')->name('provider.send');

    Route::post('/provider/send-quote', function (Request $request) use ($verifiedProvider, $notVerifiedMessage, $outsideAreaMessage) {
        $data = $request->validate([
            'service_request_id' => 'required|integer|exists:service_requests,id',
            'total_price'        => 'required|numeric|gt:0|max:999999.99',
            'price_type'         => 'nullable|in:Fixed,Estimate,Hourly',
            'included'           => 'nullable|array',
            'included.*'         => 'string|max:50',
            'not_included'       => 'nullable|array',
            'not_included.*'     => 'string|max:50',
            'validity'           => 'nullable|string|max:50',
            'note'               => 'nullable|string|max:1000',
        ]);

        if (!$verifiedProvider()) {
            return redirect()->route('provider.profile')
                ->with('error', $notVerifiedMessage());
        }

        $serviceRequest = ServiceRequest::findOrFail($data['service_request_id']);
        if ($serviceRequest->status !== 'Active') {
            return redirect()->route('provider.requests')
                ->with('error', 'This request is no longer open for quotes.');
        }

        if (!Geo::requestInArea($verifiedProvider(), $serviceRequest)) {
            return redirect()->route('provider.requests')->with('error', $outsideAreaMessage());
        }

        // One quote per provider per request
        if (Quote::where('provider_user_id', auth()->id())->where('service_request_id', $serviceRequest->id)->exists()) {
            return redirect()->route('provider.quotes')
                ->with('error', 'You have already sent a quote for this request.');
        }

        $quote = Quote::create([
            'provider_name'      => auth()->user()->name,
            'provider_user_id'   => auth()->id(),
            'total_price'        => $data['total_price'],
            'price_type'         => $data['price_type'] ?? 'Fixed',
            'included'           => implode(', ', $data['included'] ?? []),
            'not_included'       => implode(', ', $data['not_included'] ?? []),
            'validity'           => $data['validity'] ?? '48 hours',
            'note'               => $data['note'] ?? null,
            'service_request_id' => $serviceRequest->id,
        ]);

        // The quote is saved either way; a mail problem must not undo it or leak internals
        $customer = User::find($serviceRequest->user_id);
        if ($customer && $customer->email) {
            try {
                Mail::to($customer->email)->send(new NewQuoteReceived($quote));
            } catch (\Throwable $e) {
                \Log::error('New quote email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('provider.dashboard')->with('success', 'Quote sent to the customer.');
    })->name('provider.send.store');

    Route::get('/provider/requests', function () use ($providerFor) {
        $provider = $providerFor();
        $quoted = Quote::where('provider_user_id', auth()->id())->pluck('service_request_id')->all();
        return view('provider.requests', [
            'requests' => Geo::openRequestsFor($provider, $quoted),
            'needsLocation' => !Geo::hasLocation($provider),
            'radius' => $provider ? Geo::radiusOf($provider) : 50,
            'areaLabel' => optional($provider)->base_label,
        ]);
    })->name('provider.requests');

    Route::get('/provider/quotes', function () {
        $quotes = Quote::where('provider_user_id', auth()->id())->latest()->get();
        $reviews = Review::where('provider_user_id', auth()->id())->latest()->get();
        $problems = Problem::whereIn('booking_id', Booking::forProvider(auth()->id())->pluck('id'))->latest()->get();
        return view('provider.quotes', [
            'quotes' => $quotes,
            'reviews' => $reviews,
            'problems' => $problems
        ]);
    })->name('provider.quotes');

    Route::get('/provider/reviews', function () {
        $reviews = Review::where('provider_user_id', auth()->id())->latest()->get();
        return view('provider.reviews', ['reviews' => $reviews]);
    })->name('provider.reviews');

    Route::get('/provider/subscription', function () {
        return view('provider.subscription');
    })->name('provider.subscription');

    Route::get('/provider/problems', function () {
        return view('provider.problems', [
            'problems' => Problem::whereIn('booking_id', Booking::forProvider(auth()->id())->pluck('id'))->latest()->get()
        ]);
    })->name('provider.problems');

    Route::get('/provider/bookings', function () {
        $bookings = Booking::forProvider(auth()->id())->latest()->get();
        $problems = Problem::whereIn('booking_id', $bookings->pluck('id'))->latest()->get();
        return view('provider.pbook', ['bookings' => $bookings, 'problems' => $problems]);
    })->name('provider.pbook');

    // The customers this provider can chat with: anyone who messaged them, or who booked one of their quotes
    $myCustomers = function () {
        $me = auth()->id();
        $ids = Message::where('receiver_id', $me)->pluck('user_id')
            ->merge(Booking::forProvider($me)->pluck('user_id'))
            ->filter()->unique()->values();
        $users = User::whereIn('id', $ids)->get()->keyBy('id');
        // most recent conversation first
        $latest = Message::where(function ($q) use ($me) { $q->where('user_id', $me)->orWhere('receiver_id', $me); })
            ->latest()->get()
            ->map(fn ($m) => $m->user_id == $me ? $m->receiver_id : $m->user_id)->unique()->values();
        return $ids->sortBy(fn ($id) => ($pos = $latest->search($id)) === false ? 9999 : $pos)->values()
            ->map(fn ($id) => ['id' => $id, 'name' => optional($users->get($id))->name ?: 'Customer'])
            ->filter(fn ($c) => $users->has($c['id']))->values();
    };

    Route::get('/provider/messages', function (Request $request) use ($myCustomers) {
        $me = auth()->id();
        $customers = $myCustomers();
        $first = $customers->first();
        $with = (int) $request->input('with', $first['id'] ?? 0);
        if (!$customers->contains('id', $with)) {
            $with = (int) ($first['id'] ?? 0);
        }

        $messages = collect();
        if ($with) {
            $messages = Message::where(function ($outer) use ($me, $with) {
                $outer->where(function ($q) use ($me, $with) {
                    $q->where('user_id', $me)->where('receiver_id', $with);
                })->orWhere(function ($q) use ($me, $with) {
                    $q->where('user_id', $with)->where('receiver_id', $me);
                });
            })->oldest()->get();
        }

        return view('provider.messages', [
            'messages' => $messages,
            'customers' => $customers,
            'with' => $with,
            'withName' => optional($customers->firstWhere('id', $with))['name'] ?? null,
        ]);
    })->name('provider.messages');

    Route::post('/provider/messages', function (Request $request) use ($myCustomers) {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'with'    => 'required|integer',
        ]);
        // A reply always goes to the customer whose chat is open
        if (!$myCustomers()->contains('id', (int) $data['with'])) {
            return redirect()->route('provider.messages')->with('error', 'You can only message customers who have written to you or booked you.');
        }
        Message::create([
            'sender_name' => auth()->user()->name,
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'receiver_id' => (int) $data['with'],
        ]);
        return redirect()->route('provider.messages', ['with' => $data['with']]);
    })->name('provider.messages.store');

    Route::post('/provider/booking/{booking}/status', function (Request $request, Booking $booking) {
        // Provider may only update THEIR OWN bookings
        abort_unless(Booking::forProvider(auth()->id())->where('id', $booking->id)->exists(), 403);
        if ($booking->status !== 'Paid') { // work starts after the customer has paid
            return redirect()->back()->with('error', 'You can update tracking once the customer has paid for this booking.');
        }
        $data = $request->validate(['status' => 'required|in:On my way,Arrived,Loading,In transit,Delivered']);
        $booking->update(['tracking_status' => $data['status']]);
        return redirect()->route('provider.dashboard')->with('success', 'Tracking status updated!');
    })->name('provider.booking.status');

    Route::get('/provider/rate-card', function () {
        return view('provider.rate');
    })->name('provider.rate');

    Route::get('/provider/profile', function () {
        $user = auth()->user();
        $provider = Provider::firstOrCreate(['email' => $user->email], ['name' => $user->name, 'user_id' => $user->id]);
        $reviews = Review::where('provider_user_id', $user->id)->latest()->get();
        return view('provider.profile', ['provider' => $provider, 'reviews' => $reviews]);
    })->name('provider.profile');

    Route::post('/provider/profile', function (Request $request) {
        $request->validate([
            'image' => 'nullable|image|max:5120', 
            'vehicle_image' => 'nullable|image|max:5120', 
            'id_image' => 'nullable|image|max:5120',
            'id_document' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'vehicle_document' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_type' => 'nullable|in:Cheque / Current,Savings,Transmission',
            'bank_account_holder' => 'nullable|string|max:150',
            'bank_account_number' => ['nullable', 'regex:/^[0-9 ]{6,20}$/'],
            'bank_branch_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'base_label' => 'nullable|string|max:150',
            'base_lat' => 'nullable|numeric|between:-36,-21',
            'base_lng' => 'nullable|numeric|between:15,34',
            'service_radius_km' => 'nullable|in:10,25,50,100,200',
        ], [
            'bank_account_number.regex' => 'The account number must be digits only.',
            'bank_branch_code.regex' => 'The branch code must be 6 digits.',
        ]);
        
        $user = auth()->user();
        $provider = Provider::firstOrCreate(['email' => $user->email], ['name' => $user->name, 'user_id' => $user->id]);
        
        // The three agreements from the application form: asked once, then remembered
        $consentData = [];
        $consentErrors = [];
        foreach (['consent_display_at' => 'consent_display', 'consent_verification_at' => 'consent_accuracy', 'consent_terms_at' => 'consent_terms'] as $column => $input) {
            if ($provider->{$column}) { continue; }
            if ($request->boolean($input)) { $consentData[$column] = now(); }
            else { $consentErrors[$input] = 'Please tick this box to continue.'; }
        }
        if ($consentErrors) {
            return back()->withInput()->withErrors($consentErrors);
        }

        $data = [
            'name' => $request->name,
            'company_name' => $request->company_name,
            'vehicle' => $request->vehicle,
            'plate_number' => $request->plate_number,
            'bank_name' => $request->bank_name,
            'bank_account_type' => $request->bank_account_type,
            'bank_account_holder' => $request->bank_account_holder,
            'bank_branch_code' => $request->bank_branch_code,
        ];
        // Service area: the phone's location if the button was used, otherwise look the typed place up
        $label = trim((string) $request->base_label);
        $data['base_label'] = $label !== '' ? $label : $provider->base_label;
        $data['service_radius_km'] = (int) ($request->service_radius_km ?: ($provider->service_radius_km ?: 50));
        if ($request->filled('base_lat') && $request->filled('base_lng')) {
            $data['base_lat'] = (float) $request->base_lat;
            $data['base_lng'] = (float) $request->base_lng;
            $data['base_label'] = $label !== '' ? $label : 'My current location';
        } elseif ($label !== '' && ($label !== (string) $provider->base_label || $provider->base_lat === null)) {
            $found = Geo::geocode($label);
            if (!$found) {
                return back()->withInput()->withErrors(['base_label' => 'We could not find "' . $label . '". Add the suburb and city, for example Sandton, Johannesburg.']);
            }
            $data['base_lat'] = $found['lat'];
            $data['base_lng'] = $found['lng'];
        }

        // Only replace the stored account number when a new one is typed in
        if (filled($request->bank_account_number)) {
            $data['bank_account_number'] = preg_replace('/\s+/', '', $request->bank_account_number);
        }

        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('providers', 's3');
        if ($request->hasFile('vehicle_image')) $data['vehicle_image_path'] = $request->file('vehicle_image')->store('providers', 's3');
        // ID and vehicle documents go to the PRIVATE bucket
        foreach ([['id_image', 'id_image_path'], ['id_document', 'id_document'], ['vehicle_document', 'vehicle_document']] as [$input, $column]) {
            if ($request->hasFile($input)) {
                if (!Docs::available()) {
                    return back()->withInput()->with('error', 'Secure document storage is not set up yet, so your documents could not be saved. Please try again later.');
                }
                $data[$column] = Docs::store($request->file($input));
            }
        }
        
        $provider->update($data + $consentData);
        
        return redirect()->route('provider.profile')->with('success', 'Profile updated successfully!');
    })->name('provider.profile.update');

    // PROVIDER LIVE TRACKING ROUTES
    Route::get('/provider/tracking', [TrackingController::class, 'providerMap'])->name('provider.tracking');
    Route::post('/provider/tracking/update', [TrackingController::class, 'updateLocation'])->name('provider.tracking.update');

    }); // end Provider

    // ADMIN ROUTES
    Route::middleware('role:Admin')->group(function () {

    // Removes one account and everything that belongs to it.
    // It refuses when real money is involved (a paid booking), so records are never lost.
    $removeAccount = function (User $user) {
        if ($user->role === 'Admin' || $user->id === auth()->id()) {
            return [false, 'Admin accounts cannot be removed here.'];
        }
        $id = $user->id;

        $requestIds = ServiceRequest::where('user_id', $id)->pluck('id');
        $quoteIds = Quote::where(function ($q) use ($id, $requestIds) {
            $q->where('provider_user_id', $id)->orWhereIn('service_request_id', $requestIds);
        })->pluck('id');
        $bookings = Booking::where(function ($q) use ($id, $quoteIds) {
            $q->where('user_id', $id)->orWhereIn('quote_id', $quoteIds);
        })->get();

        if ($bookings->where('status', 'Paid')->isNotEmpty()) {
            return [false, $user->name . ' has paid bookings, so the account was kept for your records.'];
        }

        $bookingIds = $bookings->pluck('id');
        $provider = Provider::where('email', $user->email)->first();
        $files = $provider ? array_filter([
            $provider->image_path, $provider->vehicle_image_path, $provider->id_image_path,
            $provider->id_document, $provider->vehicle_document,
        ]) : [];
        $name = $user->name;

        DB::transaction(function () use ($user, $id, $requestIds, $quoteIds, $bookingIds) {
            Problem::where('user_id', $id)->orWhereIn('booking_id', $bookingIds)->delete();
            ProviderLocation::where('user_id', $id)->delete();
            Booking::whereIn('id', $bookingIds)->delete();
            Quote::whereIn('id', $quoteIds)->delete();
            ServiceRequest::where('user_id', $id)->delete();
            Message::where('user_id', $id)->orWhere('receiver_id', $id)->delete();
            Review::where('user_id', $id)->orWhere('provider_user_id', $id)->delete();
            ViewingRequest::where('user_id', $id)->delete();
            Property::where('accepted_by', $id)->where('status', 'Under Offer')->update(['accepted_by' => null, 'status' => 'Approved']);
            Property::where('accepted_by', $id)->update(['accepted_by' => null]);
            Property::where('user_id', $id)->delete();
            Customer::where('user_id', $id)->delete();
            Provider::where('email', $user->email)->delete();
            $user->delete();
        });

        // Uploaded documents go too (a failure here must not undo the removal)
        try {
            if ($files) {
                Docs::deleteAll(array_values($files));
            }
        } catch (\Throwable $e) {
            \Log::warning('Could not delete files of removed account: ' . $e->getMessage());
        }

        return [true, 'Removed ' . $name . ' and everything linked to them.'];
    };

    Route::post('/admin/users/{user}/delete', function (User $user) use ($removeAccount) {
        [$ok, $message] = $removeAccount($user);
        return redirect()->route('admin.users')->with($ok ? 'success' : 'error', $message);
    })->name('admin.users.delete');

    Route::post('/admin/providers/{provider}/delete', function (Provider $provider) use ($removeAccount) {
        $user = User::where('email', $provider->email)->first();
        if ($user) {
            [$ok, $message] = $removeAccount($user);
        } else {
            // A provider record with no login behind it
            $files = array_filter([$provider->image_path, $provider->vehicle_image_path, $provider->id_image_path, $provider->id_document, $provider->vehicle_document]);
            $name = $provider->name;
            $provider->delete();
            try { if ($files) { Docs::deleteAll(array_values($files)); } } catch (\Throwable $e) {}
            [$ok, $message] = [true, 'Removed ' . $name . '.'];
        }
        return redirect()->route('admin.providers')->with($ok ? 'success' : 'error', $message);
    })->name('admin.providers.delete');

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
        $totalRevenue = Booking::where('status', 'Paid')->sum('total_price'); // only money really received
        return view('admin.payments', ['bookings' => $bookings, 'totalRevenue' => $totalRevenue, 'pendingPayouts' => $totalRevenue * (1 - config('services.mrrel.commission_rate')), 'totalBookings' => $bookings->count()]);
    })->name('admin.payments');

    Route::post('/admin/bookings/{booking}/payout', function (Booking $booking) {
        if ($booking->status !== 'Paid') {
            return redirect()->route('admin.payments')->with('error', 'Only paid bookings can be paid out.');
        }
        $booking->update(['paid_out_at' => $booking->paid_out_at ? null : now()]);
        return redirect()->route('admin.payments')->with('success', $booking->paid_out_at ? 'Marked as paid out.' : 'Payout mark removed.');
    })->name('admin.bookings.payout');

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
    Route::get('/admin/tracking/locations', [TrackingController::class, 'adminLocations'])->name('admin.tracking.locations');
    }); // end Admin
});
