@extends('layouts.admin')

@section('content')
<link href="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.css" rel="stylesheet" />
<style>
    #map { height: 500px; width: 100%; border-radius: 12px; border: 1px solid var(--l); margin-top: 16px; }
</style>

<h1>Admin Live Tracking</h1>
<p class="m" style="margin-bottom: 16px;">Monitor active providers in real-time.</p>

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

    const markers = {};
    let firstFit = true;
    const statusText = document.getElementById('statusText');

    function fetchProviderLocations() {
        fetch('/admin/tracking/locations', { headers: { 'Accept': 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error('Server error ' + response.status);
                return response.json();
            })
            .then(list => {
                const online = list.filter(p => p.online);

                // Remove pins of providers that are no longer online
                Object.keys(markers).forEach(id => {
                    if (!online.find(p => String(p.id) === id)) {
                        markers[id].remove();
                        delete markers[id];
                    }
                });

                online.forEach(p => {
                    const lngLat = [parseFloat(p.longitude), parseFloat(p.latitude)];
                    if (markers[p.id]) {
                        markers[p.id].setLngLat(lngLat);
                    } else {
                        markers[p.id] = new maplibregl.Marker({ color: "#10b981" })
                            .setLngLat(lngLat)
                            .setPopup(new maplibregl.Popup({ offset: 25 }).setText(p.name))
                            .addTo(map);
                    }
                });

                if (online.length === 0) {
                    statusText.textContent = 'No providers are currently sharing their location.';
                    return;
                }

                statusText.textContent = online.length + ' provider(s) online: ' +
                    online.map(p => p.name + ' (' + p.updated_at + ')').join(', ');

                // Zoom to the providers once, so the map does not jump every 10 seconds
                if (firstFit) {
                    firstFit = false;
                    const bounds = new maplibregl.LngLatBounds();
                    online.forEach(p => bounds.extend([parseFloat(p.longitude), parseFloat(p.latitude)]));
                    map.fitBounds(bounds, { padding: 80, maxZoom: 15 });
                }
            })
            .catch(error => {
                statusText.textContent = 'Could not load provider locations.';
                console.error("Error fetching locations:", error);
            });
    }

    map.on('load', fetchProviderLocations);
    setInterval(fetchProviderLocations, 10000);
</script>
@endsection