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
        <h1>Payment Received!</h1>
        <p>Hi {{ $booking->customer_name }},</p>
        <p>Thank you for your payment. Your booking (BKG-{{ $booking->id }}) has been successfully paid.</p>
        <p><strong>Total Paid:</strong> R{{ number_format($booking->total_price, 2) }}</p>
        <p>Your provider will be in touch shortly and you can track their arrival live on the platform.</p>
        <a href="{{ url('/customer/tracking') }}" class="btn">Track Provider</a>
        <p style="margin-top: 20px; font-size: 12px; color: #51657A;">This is an automated message from MrRel.</p>
    </div>
</body>
</html>