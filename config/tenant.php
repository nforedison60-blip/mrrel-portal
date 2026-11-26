<?php

// Fees and settings for the tenant process (finding a home).
return [
    // Once-off fee a tenant pays to see the full details of their matches
    'matching_fee' => (float) env('MRREL_MATCHING_FEE', 99),

    // Optional: someone from MrRel attends a viewing on the tenant's behalf
    'attendance' => [
        'base_fee'    => (float) env('MRREL_ATTENDANCE_BASE_FEE', 150),
        'included_km' => (float) env('MRREL_ATTENDANCE_INCLUDED_KM', 10),
        'per_km'      => (float) env('MRREL_ATTENDANCE_PER_KM', 7.5),
        'road_factor' => (float) env('MRREL_ATTENDANCE_ROAD_FACTOR', 1.3),
    ],

    // Where the MrRel attendee travels from. If lat or lng is empty, the attendance option is hidden.
    'base' => [
        'label' => env('MRREL_BASE_LABEL'),
        'lat'   => env('MRREL_BASE_LAT'),
        'lng'   => env('MRREL_BASE_LNG'),
    ],
];
