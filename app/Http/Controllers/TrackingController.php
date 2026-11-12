<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;
use App\Models\Booking;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function providerMap()
    {
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

        ProviderLocation::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'booking_id' => $request->booking_id,
                'updated_at' => now(), // Force the timestamp to update
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
        $location = null;

        if ($request->has('booking_id') && $request->booking_id != '' && $request->booking_id != 'null') {
            $location = ProviderLocation::where('booking_id', $request->booking_id)->latest('updated_at')->first();
        }

        if (!$location) {
            $location = ProviderLocation::latest('updated_at')->first();
        }

        if ($location) {
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

        return response()->json(['status' => 'no_location_found']);
    }

    public function adminMap()
    {
        return view('admin.map');
    }
}