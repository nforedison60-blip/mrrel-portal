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
    <p id="statusText" class="m" style="margin-bottom: 12px; color: #eab308;">Waiting for GPS...</p>
    
    <button id="trackBtn" class="btn" style="background: #ef4444; color: white; margin-bottom: 16px; display: none;">
        Stop Tracking
    </button>
    
    <div id="map"></div>
</div>

<script src="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.js"></script>
<script>
    // 1. Setup Map
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 2. Start Tracking automatically
    function startTracking() {
        if (!navigator.geolocation) {
            statusText.textContent = "Geolocation is not supported by your browser.";
            return;
        }

        statusText.textContent = "Tracking active. Waiting for first GPS ping...";
        trackBtn.style.display = 'inline-block';
        
        // Send immediately
        updateLocation();
        
        // Then send every 10 seconds
        trackingInterval = setInterval(updateLocation, 10000); 
    }

    // 3. Get location and send to Laravel
    function updateLocation() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Move map and place pin
                map.flyTo({ center: [lng, lat], zoom: 16 });
                if (marker) {
                    marker.setLngLat([lng, lat]);
                } else {
                    marker = new maplibregl.Marker({ color: "#10b981" })
                        .setLngLat([lng, lat])
                        .addTo(map);
                }

                // Send to database
                fetch('/provider/tracking/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ 
                        latitude: lat, 
                        longitude: lng 
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server error');
                    return response.json();
                })
                .then(data => {
                    statusText.textContent = 'Tracking active. Last sent: ' + new Date().toLocaleTimeString();
                })
                .catch(error => {
                    statusText.textContent = 'Error saving location to database.';
                    console.error(error);
                });
            },
            (error) => {
                // If the user blocks location or it times out
                statusText.textContent = "Please allow location access in your browser settings to track.";
                console.error("GPS Error:", error);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // 4. Stop button logic
    trackBtn.addEventListener('click', () => {
        clearInterval(trackingInterval);
        trackingInterval = null;
        trackBtn.style.display = 'none';
        statusText.textContent = 'Tracking stopped.';
    });

    // Initialize
    startTracking();
</script>
@endsection