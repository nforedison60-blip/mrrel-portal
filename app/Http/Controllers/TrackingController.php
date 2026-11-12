<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;
use App\Models\Booking;

class TrackingController extends Controller
{
    public function providerMap()
    {
        // Get the latest booking so the provider can attach their GPS to it
        $booking = Booking::latest()->first();
        return view('provider.tracking', ['booking' => $booking]);
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'booking_id' => 'nullable'
        ]);

        // Save the location. If no booking_id is sent, it saves as null.
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

    public function customerMap()
    {
        $booking = Booking::latest()->first();
        return view('customer.tracking', ['booking' => $booking]);
    }

    public function getLatestLocation(Request $request)
    {
        // BULLETPROOF LOGIC: 
        // 1. Try to find the location for the specific booking.
        // 2. If that fails (or no booking ID is sent), fallback to the absolute latest GPS ping.
        
        $location = null;

        if ($request->has('booking_id') && $request->booking_id != '' && $request->booking_id != 'null') {
            $location = ProviderLocation::where('booking_id', $request->booking_id)->latest('updated_at')->first();
        }

        if (!$location) {
            $location = ProviderLocation::latest('updated_at')->first();
        }

        if ($location) {
            return response()->json([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'updated_at' => $location->updated_at->diffForHumans(),
            ]);
        }

        return response()->json(['status' => 'no_location_found']);
    }

    public function adminMap()
    {
        return view('admin.map');
    }
}