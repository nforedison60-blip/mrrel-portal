@extends($layout)

@section('content')
<h1>Pay by bank transfer</h1>

<div class="card" style="max-width: 560px; margin-top: 20px;">
    @if($info['paid'])
        <h3>Already paid</h3>
        <p class="m">{{ $info['label'] }} is paid. Thank you.</p>
        <a href="{{ $info['back'] }}" class="btn" style="text-decoration:none;">Back</a>
    @else
        <div class="m sm">{{ $info['label'] }}</div>
        <div class="big" style="margin: 6px 0 14px;">R{{ number_format($info['amount'], 2) }}</div>

        @if($ready)
            <table class="tb" style="width:100%;">
                @if($bank['name'])<tr><td>Bank</td><td style="text-align:right;">{{ $bank['name'] }}</td></tr>@endif
                <tr><td>Account holder</td><td style="text-align:right;">{{ $bank['holder'] }}</td></tr>
                <tr><td>Account number</td><td style="text-align:right;">{{ $bank['account'] }}</td></tr>
                @if($bank['type'])<tr><td>Account type</td><td style="text-align:right;">{{ $bank['type'] }}</td></tr>@endif
                @if($bank['branch'])<tr><td>Branch code</td><td style="text-align:right;">{{ $bank['branch'] }}</td></tr>@endif
                <tr><td><b>Reference</b></td><td style="text-align:right;"><b id="bankRef" style="font-size:18px;">{{ $reference }}</b></td></tr>
                <tr><td>Amount</td><td style="text-align:right;"><b>R{{ number_format($info['amount'], 2) }}</b></td></tr>
            </table>

            <p class="m sm" style="margin-top: 12px;">Use <b>{{ $reference }}</b> as the payment reference, exactly as shown, so we can match your payment.</p>

            <div style="margin-top: 14px; padding: 12px; border-radius: 10px; background: #FFF0E5;">
                <b>Then email your proof of payment</b>
                <p class="m sm" style="margin: 4px 0 10px;">Send the proof of payment (POP) to <b>{{ $popEmail }}</b> and put <b>{{ $reference }}</b> in the subject.</p>
                <a class="btn" style="text-decoration:none;" href="mailto:{{ $popEmail }}?subject={{ rawurlencode('Proof of payment ' . $reference) }}&body={{ rawurlencode('Hi MrRel, please find my proof of payment for ' . $reference . ' (R' . number_format($info['amount'], 2) . ').') }}">Email proof of payment</a>
                <button type="button" class="btn g" id="copyRef" style="margin-left: 6px;">Copy reference</button>
            </div>

            <p class="m sm" style="margin-top: 12px;">MrRel marks this as paid once the money is in our account and we have your proof of payment. Instant EFT online is faster, so you can use that instead.</p>
        @else
            <p class="m">Bank details are not available right now. Please pay online instead.</p>
        @endif

        <p class="m sm" style="margin-top: 12px;"><a href="{{ $info['back'] }}" style="color:inherit;">Back</a></p>
    @endif
</div>

<script>
    (function () {
        var b = document.getElementById('copyRef');
        if (!b) return;
        b.addEventListener('click', function () {
            var t = document.getElementById('bankRef').textContent.trim();
            if (navigator.clipboard) { navigator.clipboard.writeText(t); }
            b.textContent = 'Copied';
        });
    })();
</script>
@endsection
