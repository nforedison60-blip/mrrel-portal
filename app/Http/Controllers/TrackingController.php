<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;
use App\Models\Booking;

class TrackingController extends Controller
{
    // Provider Map
    public function providerMap()
    {
        // Find the provider's most recent active booking to track
        $booking = Booking::where('status', '!=', 'Complete')->latest()->first();
        return view('provider.tracking', ['booking' => $booking]);
    }

    // Save GPS from Provider
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'booking_id' => 'nullable|exists:bookings,id'
        ]);

        ProviderLocation::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'booking_id' => $request->booking_id,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // Customer Map
    public function customerMap()
    {
        // Assuming the customer only has one active booking right now
        $booking = Booking::where('status', '!=', 'Complete')->latest()->first();
        return view('customer.tracking', ['booking' => $booking]);
    }

    // Fetch Latest GPS for Customer/Admin (Specific to a Booking)
    public function getLatestLocation(Request $request)
    {
        $query = ProviderLocation::query();

        // If a booking_id is passed, only get the location for that booking
        if ($request->has('booking_id') && $request->booking_id != '') {
            $query->where('booking_id', $request->booking_id);
        }

        $location = $query->latest('updated_at')->first();

        if ($location) {
            return response()->json([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'updated_at' => $location->updated_at->diffForHumans(),
            ]);
        }

        return response()->json(['status' => 'no_location_found']);
    }

    // Admin Map
    public function adminMap()
    {
        return view('admin.map');
    }
}