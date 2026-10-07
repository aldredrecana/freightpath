<?php

return [
    // A pending or declined trip is flagged URGENT this many hours before departure.
    'late_window_hours' => (int) env('FREIGHTPATH_LATE_WINDOW_HOURS', 3),

    // After this many minutes without acceptance, "Awaiting driver" turns red.
    'response_window_minutes' => (int) env('FREIGHTPATH_RESPONSE_WINDOW_MINUTES', 30),

    'public_registration' => (bool) env('FREIGHTPATH_PUBLIC_REGISTRATION', false),

    'dispatcher_phone' => env('FREIGHTPATH_DISPATCH_PHONE'),
];