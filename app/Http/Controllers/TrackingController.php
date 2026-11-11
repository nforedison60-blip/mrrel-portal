<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderLocation;

class TrackingController extends Controller
{
    // This shows the map to the provider
    public function providerMap()
    {
        return view('provider.tracking');
    }

    // This receives the GPS coordinates from the phone
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Save or update the provider's location in the database
        ProviderLocation::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // NEW: This shows the map to the customer
    public function customerMap()
    {
        return view('customer.tracking');
    }

    // NEW: This fetches the latest GPS coordinates for the customer map
    public function getLatestLocation()
    {
        // For this example, we just grab the most recently updated provider location
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
}