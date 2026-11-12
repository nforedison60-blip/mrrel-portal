<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;
use App\Models\Booking;

class TrackingController extends Controller
{
    public function providerMap()
    {
        return view('provider.tracking');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        ProviderLocation::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function customerMap()
    {
        return view('customer.tracking');
    }

    public function getLatestLocation()
    {
        // Just get the absolute latest ping. No booking_id filters for now.
        $location = ProviderLocation::latest('updated_at')->first();

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