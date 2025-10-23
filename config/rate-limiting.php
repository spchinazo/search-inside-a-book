<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Here you may configure rate limiting for your application. These limits
    | are used to prevent abuse and ensure fair usage of your application.
    |
    */

    'api' => [
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],

    'search' => [
        'max_attempts' => 30,
        'decay_minutes' => 1,
    ],
];
