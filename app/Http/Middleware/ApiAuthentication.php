<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Services\ApiRateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    protected ApiRateLimitService $rateLimitService;

    public function __construct(ApiRateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from Authorization header or query parameter
        $apiKeyValue = $this->extractApiKey($request);

        if (!$apiKeyValue) {
            return response()->json([
                'error' => 'API key required',
                'message' => 'Please provide an API key in the Authorization header or as a query parameter',
            ], 401);
        }

        // Find the API key
        $apiKey = ApiKey::where('key', $apiKeyValue)->first();

        if (!$apiKey || !$apiKey->isValid()) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or has expired',
            ], 401);
        }

        // Check if user has API access
        if (!$this->rateLimitService->hasApiAccess($apiKey->user)) {
            return response()->json([
                'error' => 'API access not available',
                'message' => 'API access is only available for Pro and Business plans',
                'upgrade_required' => true,
            ], 403);
        }

        // Check rate limits
        $rateLimitCheck = $this->rateLimitService->checkRateLimit(
            $apiKey->user,
            $request->route()?->uri() ?? $request->path(),
            $request->method(),
            $apiKey
        );

        if (!$rateLimitCheck['allowed']) {
            $response = response()->json([
                'error' => 'Rate limit exceeded',
                'message' => $rateLimitCheck['reason'],
                'retry_after' => $rateLimitCheck['retry_after'] ?? null,
                'current_count' => $rateLimitCheck['current_count'] ?? null,
                'limit' => $rateLimitCheck['limit'] ?? null,
            ], 429);

            if (isset($rateLimitCheck['retry_after'])) {
                $response->header('Retry-After', $rateLimitCheck['retry_after']);
            }

            return $response;
        }

        // Add user and API key to request
        $request->setUserResolver(function () use ($apiKey) {
            return $apiKey->user;
        });
        $request->attributes->set('api_key', $apiKey);

        // Record the request
        $this->rateLimitService->recordRequest(
            $apiKey->user,
            $request->route()?->uri() ?? $request->path(),
            $request->method(),
            $apiKey
        );

        // Add rate limit headers to response
        $response = $next($request);
        
        $currentCounts = $rateLimitCheck['current_counts'] ?? [];
        $limits = $rateLimitCheck['limits'] ?? [];

        $response->headers->set('X-RateLimit-Limit-Minute', $limits['requests_per_minute'] ?? 0);
        $response->headers->set('X-RateLimit-Remaining-Minute', max(0, ($limits['requests_per_minute'] ?? 0) - ($currentCounts['minute'] ?? 0)));
        $response->headers->set('X-RateLimit-Limit-Hour', $limits['requests_per_hour'] ?? 0);
        $response->headers->set('X-RateLimit-Remaining-Hour', max(0, ($limits['requests_per_hour'] ?? 0) - ($currentCounts['hour'] ?? 0)));
        $response->headers->set('X-RateLimit-Limit-Day', $limits['requests_per_day'] ?? 0);
        $response->headers->set('X-RateLimit-Remaining-Day', max(0, ($limits['requests_per_day'] ?? 0) - ($currentCounts['day'] ?? 0)));

        return $response;
    }

    /**
     * Extract API key from request
     */
    private function extractApiKey(Request $request): ?string
    {
        // Check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check query parameter
        return $request->query('api_key');
    }
}