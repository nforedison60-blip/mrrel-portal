@php $introFee = number_format(\App\Support\LandlordIntro::fee(), 0, '.', ' '); @endphp
<x-process-card
    title="How it works"
    sub="Four quick steps to the right tenant."
    :steps="[
        ['icon' => 'homeplus', 'title' => 'Add your property', 'text' => 'Create an account and add details and photos.'],
        ['icon' => 'search', 'title' => 'We review and match', 'text' => 'We check your listing, then find tenants who fit.'],
        ['icon' => 'user', 'title' => 'Choose to proceed', 'text' => 'See a short tenant profile. Happy with them? Confirm.', 'chip' => 'R'.$introFee.' once-off introduction fee'],
        ['icon' => 'key', 'title' => 'Meet and rent', 'text' => 'We share contact details and arrange the viewing. You agree the lease with the tenant.'],
    ]"
    :optional="['tag' => 'Private', 'title' => 'Your property is matched privately.', 'text' => 'Tenant contact details are released only after payment is confirmed.']"
    footer="Your property. The right connection. A simpler rental experience with MrRel.">
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
