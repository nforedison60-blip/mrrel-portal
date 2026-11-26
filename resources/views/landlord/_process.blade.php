@php $introFee = number_format(\App\Support\LandlordIntro::fee(), 0, '.', ' '); @endphp
<x-process-card
    title="How it works"
    sub="Find the right tenant with MrRel."
    :steps="[
        ['icon' => 'camera', 'title' => 'Register your property', 'text' => 'Add the details and photographs. We review it before matching.'],
        ['icon' => 'search', 'title' => 'We find a tenant', 'text' => 'We match tenants to your property and send a limited profile.'],
        ['icon' => 'card', 'title' => 'Confirm and pay', 'text' => 'Accept the introduction to release the contact details.', 'chip' => 'R'.$introFee.' once-off'],
        ['icon' => 'handshake', 'title' => 'Meet and finalise', 'text' => 'We coordinate the viewing. You and the tenant agree the lease.'],
    ]">
    <ol class="pfull">
        <li><b>Register your property:</b> Create an account and submit your property details and photographs.</li>
        <li><b>Property review:</b> We review your submission before making it available for private matching.</li>
        <li><b>Get matched:</b> We identify potential tenants whose requirements match your property.</li>
        <li><b>Review the tenant:</b> Receive a limited tenant profile and decide whether you would like to proceed.</li>
        <li><b>Confirm and pay:</b> If you accept the tenant introduction and agree to proceed with a viewing, pay the once-off <b>R{{ $introFee }} introduction and coordination fee</b>.</li>
        <li><b>Meet your potential tenant:</b> Once payment is confirmed, we release the necessary contact details and coordinate the viewing.</li>
        <li><b>Finalise the rental:</b> You and the tenant deal directly to negotiate and conclude the lease.</li>
    </ol>
</x-process-card>
