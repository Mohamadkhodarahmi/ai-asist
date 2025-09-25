<?php

return [
    'api_key' => env('NOWPAYMENTS_API_KEY'),
    'ipn_secret' => env('NOWPAYMENTS_IPN_SECRET'),
    'base_url' => env('NOWPAYMENTS_BASE_URL', 'https://api.nowpayments.io/v1'),
];


