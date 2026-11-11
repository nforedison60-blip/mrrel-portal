<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;

class TrackingController extends Controller
{
    // Provider Map
    public function providerMap()
    {
        return view('provider.tracking');
    }

    // Save GPS from Provider
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

    // Customer Map
    public function customerMap()
    {
        return view('customer.tracking');
    }

    // Fetch Latest GPS for Customer
    public function getLatestLocation()
    {
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

    // NEW: Admin Map
    public function adminMap()
    {
        return view('admin.map');
    }
}