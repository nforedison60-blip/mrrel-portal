<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;
use App\Models\Booking;
use Carbon\Carbon;

class TrackingController extends Controller
{
    // PROVIDER: sees only THEIR OWN latest booking
    public function providerMap()
    {
        $booking = Booking::forProvider(auth()->id())->latest()->first();
        return view('provider.tracking', ['booking' => $booking]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'booking_id' => 'nullable'
        ]);

        // Only accept a booking id that really belongs to this provider
        $bookingId = null;
        if ($request->filled('booking_id') && $request->booking_id !== 'null') {
            $bookingId = Booking::forProvider(auth()->id())
                ->where('id', $request->booking_id)
                ->value('id');
        }

        $location = ProviderLocation::firstOrNew(['user_id' => auth()->id()]);
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->booking_id = $bookingId;
        $location->updated_at = now();
        $location->save();

        return response()->json(['status' => 'success']);
    }

    // CUSTOMER: sees only THEIR OWN latest booking
    public function customerMap()
    {
        $booking = Booking::where('user_id', auth()->id())->latest()->first();
        return view('customer.tracking', ['booking' => $booking]);
    }

    public function getLatestLocation(Request $request)
    {
        // Find the customer's own booking (never someone else's)
        $bookingQuery = Booking::where('user_id', auth()->id());

        if ($request->filled('booking_id') && $request->booking_id !== 'null') {
            $bookingQuery->where('id', $request->booking_id);
        }

        $booking = $bookingQuery->latest()->first();

        if (!$booking) {
            return response()->json(['status' => 'no_location_found']);
        }

        $location = ProviderLocation::where('booking_id', $booking->id)->latest('updated_at')->first();

        if (!$location) {
            return response()->json(['status' => 'no_location_found']);
        }

        // Check if the last update was more than 2 minutes ago
        $lastUpdate = Carbon::parse($location->updated_at);
        if ($lastUpdate->diffInMinutes(now()) > 2) {
            return response()->json(['status' => 'provider_offline']);
        }

        return response()->json([
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'updated_at' => $lastUpdate->diffForHumans(),
        ]);
    }

    public function adminMap()
    {
        return view('admin.map');
    }
}
