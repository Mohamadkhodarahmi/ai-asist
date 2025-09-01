<?php
return [
    'openai' => [
        // Your key from Aval AI will go in the .env file.
        'api_key' => env('OPENAI_API_KEY'),

        // Add the base URL for Aval AI.
        // Using the Iran-specific URL as it's recommended for stability.
        'base_url' => env('OPENAI_BASE_URL', 'https://api.avalai.ir/v1'),
    ],
];
