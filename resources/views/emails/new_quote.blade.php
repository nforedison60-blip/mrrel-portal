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
        <h1>You have a new quote!</h1>
        <p>Hi there,</p>
        <p><strong>{{ $quote->provider_name }}</strong> has sent you a quote for your move.</p>
        <p><strong>Total Price:</strong> R{{ number_format($quote->total_price, 2) }}</p>
        <p>Log in to your MrRel portal to review the quote, see what's included, and accept or decline it.</p>
        <a href="{{ url('/quotes') }}" class="btn">View Quote</a>
        <p style="margin-top: 20px; font-size: 12px; color: #51657A;">This is an automated message from MrRel.</p>
    </div>
</body>
</html>