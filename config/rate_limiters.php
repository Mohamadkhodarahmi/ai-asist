<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiters
    |--------------------------------------------------------------------------
    |
    | This file is for configuring rate limiters for your application.
    | Rate limiters allow you to limit the number of requests that can be made
    | to your application within a given time period.
    |
    */

    'limiters' => [
        'api' => [
            'limit' => 60,
            'period' => 60, // 1 minute
        ],
        'telegram' => [
            'limit' => 10,
            'period' => 60, // 1 minute
        ],
    ],
];