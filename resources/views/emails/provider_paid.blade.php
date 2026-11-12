<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #10243A; line-height: 1.6; }
        .card { border: 1px solid #D5DEE6; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .btn { background: #F4B731; color: #10243A; padding: 10px 20px; text-decoration: none; border-radius: 99px; font-weight: bold; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>New Paid Job!</h1>
        <p>Hi Provider,</p>
        <p>Great news! The customer has paid for Booking #{{ $booking->id }}.</p>
        <p><strong>Amount:</strong> R{{ number_format($booking->total_price, 2) }}</p>
        <p>Please ensure you arrive on time. You can start sharing your GPS location from your dashboard.</p>
        <a href="{{ url('/provider/tracking') }}" class="btn">Start Tracking</a>
        <p style="margin-top: 20px; font-size: 12px; color: #51657A;">This is an automated message from MrRel.</p>
    </div>
</body>
</html>