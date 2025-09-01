<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    protected string $apiKey;
    protected string $baseUrl; // The service will use this

    public function __construct()
    {
        // Load both the API key and the base URL from your config file
        $this->apiKey = config('aiservices.openai.api_key');
        $this->baseUrl = config('aiservices.openai.base_url');
    }

    /**
     * Generates a vector embedding for a single string of text.
     */
    public function generateEmbedding(string $text): array
    {
        // Construct the full, correct URL by combining the base URL and the endpoint
        $fullUrl = $this->baseUrl . '/embeddings';

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($fullUrl, [ // Use the correctly constructed URL
                    'model' => 'text-embedding-3-small',
                    'input' => $text,
                ]);

            $response->throw();

            return $response->json('data.0.embedding');

        } catch (RequestException $e) {
            Log::error('Aval AI API request failed for single embedding: ' . $e->getMessage(), [
                'status' => $e->response ? $e->response->status() : 'N/A',
                'response' => $e->response ? $e->response->body() : 'N/A'
            ]);
            throw new \Exception('Failed to generate embedding from Aval AI API.');
        }
    }
}
