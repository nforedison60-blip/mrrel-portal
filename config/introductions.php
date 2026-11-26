<?php

// The landlord side of finding a tenant: MrRel introduces a matched tenant and the landlord pays once to proceed.
return [
    // Once-off introduction and coordination fee a landlord pays per tenant introduction
    'fee' => (float) env('MRREL_INTRO_FEE', 250),
];
