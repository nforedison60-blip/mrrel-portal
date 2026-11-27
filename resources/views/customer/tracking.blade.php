@extends('layouts.app')

@section('content')
<link href="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.css" rel="stylesheet" />
<style>
    #map { height: 500px; width: 100%; border-radius: 12px; border: 1px solid var(--l); margin-top: 16px; }
</style>

@php
    $providerName = optional(optional($booking)->quote)->provider_name ?: 'your provider';
    $isPaid = $booking && $booking->status === 'Paid';
@endphp

<h1>Live tracking</h1>
<p class="m" style="margin-bottom: 16px;">Watch {{ $providerName }} arrive in real time.</p>

@if(!$booking)
    <div class="card empty">
        <h3>Nothing to track yet</h3>
        <p class="m" style="margin-bottom: 12px;">Tracking starts once you have accepted a quote and booked a provider.</p>
        <a href="{{ route('customer.quotes') }}" class="btn">View my quotes</a>
    </div>
@elseif(!$isPaid)
    <div class="card empty">
        <h3>Waiting for payment</h3>
        <p class="m" style="margin-bottom: 12px;">Your provider starts work, and sharing their location, once your booking is paid.</p>
        <a href="{{ route('payment.checkout', ['booking' => $booking->id]) }}" class="btn">Pay now</a>
        <a href="{{ route('bank.show', 'BKG-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)) }}" class="btn g" style="text-decoration:none;">Pay by bank transfer</a>
    </div>
@else
    <div class="card">
        <h3>Provider status</h3>
        <p id="statusText" class="m" style="margin-bottom: 12px;">Looking for {{ $providerName }}...</p>
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/maplibre-gl@4.1.3/dist/maplibre-gl.js"></script>
    <script>
        const map = new maplibregl.Map({
            container: 'map',
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [28.0473, -26.2041],
            zoom: 11
        });

        let marker = null;
        let centred = false;
        const statusText = document.getElementById('statusText');
        const bookingId = {{ $booking->id }};

        function fetchProviderLocation() {
            fetch('/customer/tracking/latest?booking_id=' + bookingId, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'provider_offline') {
                        statusText.textContent = 'Your provider is offline right now. The map updates when they are back.';
                        if (marker) { marker.remove(); marker = null; }
                        return;
                    }

                    if (data.latitude && data.longitude) {
                        const lng = parseFloat(data.longitude);
                        const lat = parseFloat(data.latitude);

                        if (marker) {
                            marker.setLngLat([lng, lat]);
                        } else {
                            marker = new maplibregl.Marker({ color: "#10b981" }).setLngLat([lng, lat]).addTo(map);
                        }

                        // Zoom to the provider once. After that the map stays where you put it.
                        if (!centred) {
                            map.flyTo({ center: [lng, lat], zoom: 15 });
                            centred = true;
                        }

                        statusText.textContent = 'Location updated ' + data.updated_at;
                    } else {
                        statusText.textContent = 'Your provider is not sharing their location yet.';
                    }
                })
                .catch(() => {
                    statusText.textContent = 'Could not reach the server. Retrying...';
                });
        }

        map.on('load', fetchProviderLocation);
        setInterval(fetchProviderLocation, 10000);
    </script>
@endif
@endsection
