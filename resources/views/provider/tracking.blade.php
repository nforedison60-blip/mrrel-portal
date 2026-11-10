@extends('layouts.provider')

@section('content')
    <h1>Job Day: Location Sharing</h1>
    <div class="m">Keep this page open. Your location will be shared with the customer.</div>

    <div class="card" style="margin-top: 20px; max-width: 600px;">
        <h3>Tracking Status</h3>
        <p id="status" class="m">Tracking is currently OFF.</p>
        
        <div style="display: flex; gap: 8px; margin-top: 16px;">
            <button id="startBtn" class="btn">Start Tracking</button>
            <button id="stopBtn" class="btn g" style="display:none;">Stop Tracking</button>
        </div>
    </div>

    <script>
        const bookingId = 1; 
        let trackingInterval = null;
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const status = document.getElementById('status');

        function sendLocation() {
            if (!navigator.geolocation) {
                status.innerText = "Geolocation is not supported by your browser.";
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    status.innerText = `Sharing location... Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;

                    fetch(`/provider/tracking/${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ lat: lat, lng: lng })
                    });
                },
                (error) => {
                    status.innerText = "Please allow location access in your browser settings.";
                }
            );
        }

        startBtn.addEventListener('click', () => {
            startBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            sendLocation(); 
            trackingInterval = setInterval(sendLocation, 10000); 
        });

        stopBtn.addEventListener('click', () => {
            clearInterval(trackingInterval);
            startBtn.style.display = 'block';
            stopBtn.style.display = 'none';
            status.innerText = "Tracking stopped.";
        });
    </script>
@endsection