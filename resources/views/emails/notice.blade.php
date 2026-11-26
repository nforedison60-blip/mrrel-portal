<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #003068; line-height: 1.6; }
        .card { border: 1px solid #D5DEE6; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .btn { background: #FF7A1A; color: #003068; padding: 10px 20px; text-decoration: none; border-radius: 99px; font-weight: bold; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $heading }}</h1>
        @foreach($lines as $line)
            <p>{{ $line }}</p>
        @endforeach
        @if($ctaLabel && $ctaUrl)
            <a href="{{ $ctaUrl }}" class="btn">{{ $ctaLabel }}</a>
        @endif
        <p style="margin-top: 20px; font-size: 12px; color: #51657A;">This is an automated message from MrRel.</p>
    </div>
</body>
</html>
