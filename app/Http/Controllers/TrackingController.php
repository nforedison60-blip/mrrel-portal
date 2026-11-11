<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        \App\Models\ProviderLocation::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json(['status' => 'success']);
    }
}