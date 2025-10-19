<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
        \Log::info('ChatService: getAnswer called', [
            'question' => $question,
            'business_id' => $businessId,
        ]);

        $questionVector = $this->embeddingService->generateEmbedding($question);

        \Log::info('ChatService: Embedding generated', [
            'vector_length' => count($questionVector),
        ]);

        $relevantChunks = $this->vectorDbService->findSimilarChunks($questionVector, $businessId);

        \Log::info('ChatService: Chunks retrieved', [
            'chunk_count' => $relevantChunks->count(),
        ]);

        if ($relevantChunks->isEmpty()) {
            \Log::warning('ChatService: No relevant chunks found');

            return "I'm sorry, I couldn't find any relevant information to answer your question.";
        }

        $context = $relevantChunks->pluck('content')->implode("\n\n---\n\n");

        \Log::info('ChatService: Context built', [
            'context_length' => strlen($context),
        ]);

        // Collect per-file prompts tied to the retrieved chunks
        $filePrompts = $relevantChunks
            ->loadMissing('knowledgeFile')
            ->pluck('knowledgeFile.system_prompt')
            ->filter()
            ->unique()
            ->values()
            ->implode("\n- ");

        $prompt = $this->buildPrompt($context, $question, $filePrompts);

        \Log::info('ChatService: Prompt built, calling LLM', [
            'prompt_length' => strlen($prompt),
        ]);

        $answer = $this->askLanguageModel($prompt);

        \Log::info('ChatService: LLM response received', [
            'answer_length' => strlen($answer),
            'answer_preview' => substr($answer, 0, 100),
        ]);

        return $answer;
    }

    private function buildPrompt(string $context, string $question, ?string $filePrompts = null): string
    {
        $guidance = $filePrompts ? "\nAdditional Guidance (from file-specific system prompts):\n- {$filePrompts}\n" : '';

        return <<<PROMPT
        You are a helpful AI assistant. Use the following "Context" to answer the "Question".
        Your answer must be based only on the provided context. If the context does not contain the answer, say so.
        {$guidance}

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
        $fullUrl = $this->baseUrl.'/chat/completions';

        \Log::info('ChatService: Calling LLM API', [
            'url' => $fullUrl,
            'model' => 'gpt-4o-mini',
        ]);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post($fullUrl, [
                    'model' => 'gpt-4o-mini', // Changed from gpt-5-nano to gpt-4o-mini
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.2,
                    'max_tokens' => 1000,
                ]);

            \Log::info('ChatService: API response status', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            // Log the FULL response to see structure
            \Log::info('ChatService: Full API response body', [
                'body' => $response->body(),
                'json' => $response->json(),
            ]);

            if (! $response->successful()) {
                \Log::error('ChatService: API error response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            $response->throw();

            $content = $response->json('choices.0.message.content');

            \Log::info('ChatService: Extracted content from response', [
                'content_length' => strlen($content ?? ''),
                'content' => $content,
            ]);

            return $content;
        } catch (\Exception $e) {
            \Log::error('ChatService: Exception in askLanguageModel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
