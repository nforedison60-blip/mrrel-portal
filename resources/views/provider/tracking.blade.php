@extends('layouts.app') <!-- Adjust this if your layout file has a different name -->

@section('content')
<!-- MapLibre CSS -->
<link href="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.css" rel="stylesheet" />
<style>
    #map { height: 500px; width: 100%; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-top: 20px; }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Job Day: Location Sharing
        </h2>
        <p class="text-gray-600 mt-2">Keep this page open. Your location will be shared with the customer.</p>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6 p-6">
            <h3 class="text-lg font-medium text-gray-900">Tracking Status</h3>
            <p id="statusText" class="text-gray-600 mt-1">Please allow location access in your browser settings.</p>
            
            <button id="trackBtn" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Stop Tracking
            </button>

            <!-- The Map Container -->
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- MapLibre JS -->
<script src="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.js"></script>
<script>
    const map = new maplibregl.Map({
        container: 'map',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: [28.0473, -26.2041], // Default Johannesburg
        zoom: 13
    });

    let marker = null;
    let trackingInterval = null;
    const trackBtn = document.getElementById('trackBtn');
    const statusText = document.getElementById('statusText');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Start tracking automatically when page loads
    startTracking();

    // Make the button stop tracking
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
                    body: JSON.stringify({ latitude: lat, longitude: lng })
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