<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatService
{
    protected EmbeddingService $embeddingService;
    protected VectorDatabaseService $vectorDbService;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct(EmbeddingService $embeddingService, VectorDatabaseService $vectorDbService)
    {
        $this->embeddingService = $embeddingService;
        $this->vectorDbService = $vectorDbService;

        // Load credentials from the same config file.
        $this->apiKey = config('aiservices.openai.api_key');
        $this->baseUrl = config('aiservices.openai.base_url');
    }

    public function getAnswer(string $question, int $businessId): string
    {
        // Cache embeddings for 24 hours
        $cacheKey = "embedding:" . md5($question);
        $questionVector = Cache::remember($cacheKey, 86400, function () use ($question) {
            return $this->embeddingService->generateEmbedding($question);
        });

        $relevantChunks = $this->vectorDbService->findSimilarChunks($questionVector, $businessId);

        if ($relevantChunks->isEmpty()) {
            return "I'm sorry, I couldn't find any relevant information to answer your question.";
        }

        $context = $relevantChunks->take(3)->pluck('content')->implode("\n\n---\n\n");
        $prompt = $this->buildPrompt($context, $question);

        return $this->askLanguageModel($prompt);
    }

    private function buildPrompt(string $context, string $question): string
    {
        return <<<PROMPT
        You are a helpful AI assistant. Use the following "Context" to answer the "Question".
        Your answer must be based only on the provided context. If the context does not contain the answer, say so.
        Be concise and direct in your response.

        Context:
        ---
        {$context}
        ---

        Question: {$question}
        Answer:
        PROMPT;
    }

    private function askLanguageModel(string $prompt): string
    {
        // Construct the full URL using the base URL.
        $fullUrl = $this->baseUrl . '/chat/completions';

        $response = Http::withToken($this->apiKey)
            ->timeout(15) // Reduced timeout to 15 seconds
            ->post($fullUrl, [
                'model' => 'gpt-5-nano',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.2,
                'max_tokens' => 500, // Reduced max tokens for faster response
            ]);

        $response->throw();
        return $response->json('choices.0.message.content');
    }
}
