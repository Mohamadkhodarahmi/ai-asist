<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\ApiRateLimitService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    protected ApiRateLimitService $rateLimitService;
    protected AnalyticsService $analyticsService;

    public function __construct(ApiRateLimitService $rateLimitService, AnalyticsService $analyticsService)
    {
        $this->rateLimitService = $rateLimitService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get API usage statistics
     */
    public function getUsageStats(Request $request)
    {
        $user = $request->user();
        $days = $request->query('days', 30);

        $stats = $this->rateLimitService->getUsageStats($user, $days);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get rate limits for the current user
     */
    public function getRateLimits(Request $request)
    {
        $user = $request->user();
        $rateLimits = $this->rateLimitService->getRateLimitsForUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'rate_limits' => $rateLimits,
                'has_api_access' => $this->rateLimitService->hasApiAccess($user),
            ],
        ]);
    }

    /**
     * Test API endpoint
     */
    public function test(Request $request)
    {
        $user = $request->user();
        $apiKey = $request->attributes->get('api_key');

        return response()->json([
            'success' => true,
            'message' => 'API is working correctly',
            'data' => [
                'user_id' => $user->id,
                'api_key_name' => $apiKey?->name,
                'timestamp' => now()->toISOString(),
                'rate_limits' => $this->rateLimitService->getRateLimitsForUser($user),
            ],
        ]);
    }

    /**
     * Chat with AI endpoint
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'context' => 'nullable|string|max:10000',
        ]);

        $user = $request->user();
        $message = $request->input('message');
        $context = $request->input('context');

        // Here you would integrate with your AI service
        // For now, we'll return a mock response
        $response = [
            'success' => true,
            'data' => [
                'message' => $message,
                'response' => 'This is a mock AI response. In a real implementation, this would call your AI service.',
                'timestamp' => now()->toISOString(),
                'user_id' => $user->id,
            ],
        ];

        // Track the API usage for analytics
        $this->analyticsService->trackUserActivity($user, 'api_chat', [
            'message_length' => strlen($message),
            'has_context' => !empty($context),
            'api_key_id' => $request->attributes->get('api_key')?->id,
        ]);

        return response()->json($response);
    }

    /**
     * Get documents endpoint
     */
    public function getDocuments(Request $request)
    {
        $user = $request->user();

        // Get user's documents (you'll need to implement this based on your document model)
        $documents = []; // Placeholder - implement based on your document structure

        return response()->json([
            'success' => true,
            'data' => [
                'documents' => $documents,
                'total_count' => count($documents),
            ],
        ]);
    }

    /**
     * Search documents endpoint
     */
    public function searchDocuments(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $user = $request->user();
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        // Here you would implement document search
        // For now, we'll return a mock response
        $results = []; // Placeholder - implement document search

        // Track the API usage for analytics
        $this->analyticsService->trackUserActivity($user, 'api_search', [
            'query' => $query,
            'limit' => $limit,
            'results_count' => count($results),
            'api_key_id' => $request->attributes->get('api_key')?->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'query' => $query,
                'results' => $results,
                'total_count' => count($results),
            ],
        ]);
    }
}