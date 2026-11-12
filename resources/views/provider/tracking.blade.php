@extends('layouts.provider')

@section('content')
<link href="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.css" rel="stylesheet" />
<style>
    #map { height: 500px; width: 100%; border-radius: 12px; border: 1px solid var(--l); margin-top: 16px; }
</style>

<h1>Job Day: Location Sharing</h1>
<p class="m" style="margin-bottom: 16px;">Keep this page open. Your location will be shared with the customer.</p>

<div class="card">
    <h3>Tracking Status</h3>
    <p id="statusText" class="m" style="margin-bottom: 12px;">Please allow location access in your browser settings.</p>
    
    <button id="trackBtn" class="btn">Stop Tracking</button>
    <div id="map"></div>
</div>

<script src="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Grab the active booking ID from Laravel (or null if none exists)
    const bookingId = {{ $booking ? $booking->id : 'null' }};

    const map = new maplibregl.Map({
        container: 'map',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: [28.0473, -26.2041], 
        zoom: 13
    });

    let marker = null;
    let trackingInterval = null;
    const trackBtn = document.getElementById('trackBtn');
    const statusText = document.getElementById('statusText');

    startTracking();

    trackBtn.addEventListener('click', () => {
        clearInterval(trackingInterval);
        trackingInterval = null;
        trackBtn.textContent = 'Start Tracking';
        statusText.textContent = 'Tracking stopped.';
    });

    function startTracking() {
        if (!navigator.geolocation) {
            statusText.textContent = "Geolocation is not supported by your browser";
            return;
        }

        if (!bookingId) {
            statusText.textContent = "Warning: No active booking found. GPS will send without a job ID.";
        }

        trackBtn.textContent = 'Stop Tracking';
        statusText.textContent = 'Tracking active. Sending location...';
        
        updateLocation();
        trackingInterval = setInterval(updateLocation, 10000); 
    }

    function updateLocation() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.flyTo({ center: [lng, lat], zoom: 16 });
                
                if (marker) {
                    marker.setLngLat([lng, lat]);
                } else {
                    marker = new maplibregl.Marker({ color: "#10b981" })
                        .setLngLat([lng, lat])
                        .addTo(map);
                }

                fetch('/provider/tracking/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ 
                        latitude: lat, 
                        longitude: lng,
                        booking_id: bookingId // Send the booking ID!
                    })
                })
                .then(response => response.json())
                .then(data => {
                    statusText.textContent = 'Tracking active. Last sent: ' + new Date().toLocaleTimeString();
                })
                .catch(error => console.error("Error saving location:", error));
            },
            (error) => {
                console.error("Error: ", error);
                statusText.textContent = "Please allow location access to track.";
            },
            { enableHighAccuracy: true }
        );
    }
</script>
@endsection