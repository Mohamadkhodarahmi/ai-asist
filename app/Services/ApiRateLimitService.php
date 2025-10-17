<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\ApiRateLimit;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApiRateLimitService
{
    /**
     * Get rate limits for a user based on their plan
     */
    public function getRateLimitsForUser(User $user): array
    {
        $planSlug = $user->plan?->slug ?? 'free';

        return match ($planSlug) {
            'free' => [
                'requests_per_minute' => 0, // No API access
                'requests_per_hour' => 0,
                'requests_per_day' => 0,
            ],
            'starter' => [
                'requests_per_minute' => 0, // No API access
                'requests_per_hour' => 0,
                'requests_per_day' => 0,
            ],
            'pro' => [
                'requests_per_minute' => 60,
                'requests_per_hour' => 1000,
                'requests_per_day' => 10000,
            ],
            'business' => [
                'requests_per_minute' => 120,
                'requests_per_hour' => 5000,
                'requests_per_day' => 50000,
            ],
            'enterprise' => [
                'requests_per_minute' => 300, // Unlimited for enterprise
                'requests_per_hour' => 0, // 0 means unlimited
                'requests_per_day' => 0, // 0 means unlimited
            ],
            default => [
                'requests_per_minute' => 0,
                'requests_per_hour' => 0,
                'requests_per_day' => 0,
            ],
        };
    }

    /**
     * Check if a user has API access based on their plan
     */
    public function hasApiAccess(User $user): bool
    {
        $planSlug = $user->plan?->slug ?? 'free';
        return in_array($planSlug, ['pro', 'business', 'enterprise']);
    }

    /**
     * Check if a request is within rate limits
     */
    public function checkRateLimit(User $user, string $endpoint, string $method, ?ApiKey $apiKey = null): array
    {
        if (!$this->hasApiAccess($user)) {
            return [
                'allowed' => false,
                'reason' => 'API access not available for your plan',
                'upgrade_required' => true,
            ];
        }

        $rateLimits = $this->getRateLimitsForUser($user);
        $now = now();

        // Check per-minute limit
        $minuteStart = $now->copy()->startOfMinute();
        $minuteCount = $this->getRequestCount($user, $endpoint, $method, $minuteStart, $now, $apiKey);
        
        if ($minuteCount >= $rateLimits['requests_per_minute']) {
            return [
                'allowed' => false,
                'reason' => 'Rate limit exceeded: too many requests per minute',
                'retry_after' => $now->copy()->addMinute()->diffInSeconds($now),
                'current_count' => $minuteCount,
                'limit' => $rateLimits['requests_per_minute'],
            ];
        }

        // Check per-hour limit
        $hourStart = $now->copy()->startOfHour();
        $hourCount = $this->getRequestCount($user, $endpoint, $method, $hourStart, $now, $apiKey);
        
        if ($rateLimits['requests_per_hour'] > 0 && $hourCount >= $rateLimits['requests_per_hour']) {
            return [
                'allowed' => false,
                'reason' => 'Rate limit exceeded: too many requests per hour',
                'retry_after' => $now->copy()->addHour()->diffInSeconds($now),
                'current_count' => $hourCount,
                'limit' => $rateLimits['requests_per_hour'],
            ];
        }

        // Check per-day limit
        $dayStart = $now->copy()->startOfDay();
        $dayCount = $this->getRequestCount($user, $endpoint, $method, $dayStart, $now, $apiKey);
        
        if ($rateLimits['requests_per_day'] > 0 && $dayCount >= $rateLimits['requests_per_day']) {
            return [
                'allowed' => false,
                'reason' => 'Rate limit exceeded: too many requests per day',
                'retry_after' => $now->copy()->addDay()->diffInSeconds($now),
                'current_count' => $dayCount,
                'limit' => $rateLimits['requests_per_day'],
            ];
        }

        return [
            'allowed' => true,
            'current_counts' => [
                'minute' => $minuteCount,
                'hour' => $hourCount,
                'day' => $dayCount,
            ],
            'limits' => $rateLimits,
        ];
    }

    /**
     * Record an API request
     */
    public function recordRequest(User $user, string $endpoint, string $method, ?ApiKey $apiKey = null): void
    {
        if (!$this->hasApiAccess($user)) {
            return;
        }

        $now = now();
        
        // Record for current minute
        $this->incrementRequestCount($user, $endpoint, $method, $now->copy()->startOfMinute(), $now->copy()->endOfMinute(), $apiKey);
        
        // Record for current hour
        $this->incrementRequestCount($user, $endpoint, $method, $now->copy()->startOfHour(), $now->copy()->endOfHour(), $apiKey);
        
        // Record for current day
        $this->incrementRequestCount($user, $endpoint, $method, $now->copy()->startOfDay(), $now->copy()->endOfDay(), $apiKey);

        // Update API key last used time
        if ($apiKey) {
            $apiKey->update(['last_used_at' => $now]);
        }
    }

    /**
     * Get request count for a time window
     */
    private function getRequestCount(User $user, string $endpoint, string $method, Carbon $windowStart, Carbon $windowEnd, ?ApiKey $apiKey = null): int
    {
        $query = ApiRateLimit::where('user_id', $user->id)
            ->where('endpoint', $endpoint)
            ->where('method', $method)
            ->where('window_start', $windowStart)
            ->where('window_end', $windowEnd);

        if ($apiKey) {
            $query->where('api_key_id', $apiKey->id);
        }

        return $query->sum('requests_count');
    }

    /**
     * Increment request count for a time window
     */
    private function incrementRequestCount(User $user, string $endpoint, string $method, Carbon $windowStart, Carbon $windowEnd, ?ApiKey $apiKey = null): void
    {
        $query = [
            'user_id' => $user->id,
            'business_id' => $user->business_id,
            'api_key_id' => $apiKey?->id,
            'endpoint' => $endpoint,
            'method' => $method,
            'window_start' => $windowStart,
            'window_end' => $windowEnd,
        ];

        ApiRateLimit::updateOrCreate(
            $query,
            array_merge($query, [
                'requests_count' => DB::raw('requests_count + 1'),
            ])
        );
    }

    /**
     * Get API usage statistics for a user
     */
    public function getUsageStats(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $rateLimits = $this->getRateLimitsForUser($user);

        // Get total requests in the period
        $totalRequests = ApiRateLimit::where('user_id', $user->id)
            ->where('window_start', '>=', $startDate)
            ->sum('requests_count');

        // Get daily breakdown
        $dailyStats = ApiRateLimit::where('user_id', $user->id)
            ->where('window_start', '>=', $startDate)
            ->selectRaw('DATE(window_start) as date, SUM(requests_count) as requests')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get endpoint breakdown
        $endpointStats = ApiRateLimit::where('user_id', $user->id)
            ->where('window_start', '>=', $startDate)
            ->selectRaw('endpoint, method, SUM(requests_count) as requests')
            ->groupBy('endpoint', 'method')
            ->orderByDesc('requests')
            ->get();

        return [
            'total_requests' => $totalRequests,
            'daily_stats' => $dailyStats,
            'endpoint_stats' => $endpointStats,
            'rate_limits' => $rateLimits,
            'has_api_access' => $this->hasApiAccess($user),
        ];
    }

    /**
     * Clean up old rate limit records
     */
    public function cleanupOldRecords(int $daysToKeep = 30): void
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        ApiRateLimit::where('window_start', '<', $cutoffDate)->delete();
    }
}
