@extends('layouts.app')

@section('content')
<link href="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.css" rel="stylesheet" />
<style>
    #map { height: 500px; width: 100%; border-radius: 12px; border: 1px solid var(--l); margin-top: 16px; }
</style>

<h1>Live Provider Tracking</h1>
<p class="m" style="margin-bottom: 16px;">Watch your provider arrive in real-time.</p>

<div class="card">
    <h3>Provider Status</h3>
    <p id="statusText" class="m" style="margin-bottom: 12px;">Fetching location...</p>
    
    <div id="map"></div>
</div>

<script src="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.js"></script>
<script>
    const map = new maplibregl.Map({
        container: 'map',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: [28.0473, -26.2041], 
        zoom: 13
    });

    let marker = null;
    const statusText = document.getElementById('statusText');

    // Get the active booking ID from Laravel (passes null if no booking exists)
    const bookingId = {{ $booking ? $booking->id : 'null' }};

    // Function to fetch the provider's location
    function fetchProviderLocation() {
        // If there is no booking, we can't track a specific provider
        if (!bookingId) {
            statusText.textContent = 'No active booking found to track.';
            return;
        }

        // Fetch only the location for this specific booking
        fetch('/customer/tracking/latest?booking_id=' + bookingId)
            .then(response => response.json())
            .then(data => {
                if (data.latitude && data.longitude) {
                    const lat = parseFloat(data.latitude);
                    const lng = parseFloat(data.longitude);

                    // Move the map to the provider
                    map.flyTo({ center: [lng, lat], zoom: 16 });
                    
                    if (marker) {
                        marker.setLngLat([lng, lat]);
                    } else {
                        marker = new maplibregl.Marker({ color: "#10b981" })
                            .setLngLat([lng, lat])
                            .addTo(map);
                    }

                    statusText.textContent = 'Provider location updated ' + data.updated_at;
                } else {
                    statusText.textContent = 'Provider is not currently sharing their location.';
                }
            })
            .catch(error => {
                console.error("Error fetching location:", error);
                statusText.textContent = "Error connecting to the server.";
            });
    }

    // Fetch immediately when the page loads
    fetchProviderLocation();

    // Fetch again every 10 seconds to keep the map "live"
    setInterval(fetchProviderLocation, 10000);
</script>
@endsection