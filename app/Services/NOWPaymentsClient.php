<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NOWPaymentsClient
{
    public function __construct(
        private string $apiKey = '',
        private string $baseUrl = ''
    ) {
        $this->apiKey = $this->apiKey ?: (string) config('nowpayments.api_key');
        $this->baseUrl = $this->baseUrl ?: (string) config('nowpayments.base_url');
    }

    public function createInvoice(array $payload): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post($this->baseUrl.'/invoice', $payload);

        $response->throw();

        return $response->json();
    }
}


