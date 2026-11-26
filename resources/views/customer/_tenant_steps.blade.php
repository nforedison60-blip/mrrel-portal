<x-process-card
    title="How it works"
    sub="Four quick steps to your next home."
    :steps="[
        ['icon' => 'pencil', 'title' => 'Tell us what you need', 'text' => 'Areas, budget, bedrooms and move-in date.'],
        ['icon' => 'search', 'title' => 'We find your match', 'text' => 'We pick homes that fit what you asked for.'],
        ['icon' => 'unlock', 'title' => 'Unlock the details', 'text' => 'See the full property details.', 'chip' => 'R99 once-off'],
        ['icon' => 'home', 'title' => 'View and move in', 'text' => 'We arrange the viewing. You sign the lease with the landlord.'],
    ]"
    :optional="['title' => 'Want us to attend the viewing for you?', 'text' => 'R150 for the first 10 km round trip, then R7.50 per extra km.']">
    <ol class="pfull">
        <li><b>Tell us what you need:</b> Submit your preferred areas, budget, number of bedrooms, move-in date and other requirements.</li>
        <li><b>Get matched:</b> We identify suitable properties based on your requirements.</li>
        <li><b>Review your match:</b> Receive a property match and pay the once-off <b>R99 matching fee</b> to access the full property details.</li>
        <li><b>Arrange a viewing:</b> Request a viewing and we'll coordinate with the landlord or authorised agent.</li>
        <li><b>Optional viewing attendance:</b> Want someone from MrRel to attend on your behalf? Pay <b>R150 for the first 10 km round trip, plus R7.50 per additional kilometre</b>.</li>
        <li><b>Make your move:</b> View the property, decide whether it's right for you and finalise the lease directly with the landlord.</li>
    </ol>
</x-process-card>
