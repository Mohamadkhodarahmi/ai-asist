<?php

namespace App\Services;

use App\Models\ChatAnalytic;
use App\Models\DocumentAnalytic;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserActivityAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AnalyticsService
{
    public function trackChatInteraction(User $user, ?int $businessId, string $question, string $answer, ?int $responseTimeMs = null, ?int $tokensUsed = null, array $metadata = []): void
    {
        ChatAnalytic::create([
            'user_id' => $user->id,
            'business_id' => $businessId,
            'question' => $question,
            'answer' => $answer,
            'response_time_ms' => $responseTimeMs,
            'tokens_used' => $tokensUsed,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function trackDocumentUpload(User $user, ?int $businessId, int $knowledgeFileId, string $documentName, string $documentType, int $fileSizeBytes, ?int $pagesCount = null): void
    {
        DocumentAnalytic::updateOrCreate(
            [
                'user_id' => $user->id,
                'business_id' => $businessId,
                'knowledge_file_id' => $knowledgeFileId,
            ],
            [
                'document_name' => $documentName,
                'document_type' => $documentType,
                'file_size_bytes' => $fileSizeBytes,
                'pages_count' => $pagesCount,
                'uploaded_at' => now(),
                'last_accessed_at' => now(),
            ]
        );
    }

    public function incrementDocumentQuestions(User $user, ?int $businessId, string $documentName): void
    {
        DocumentAnalytic::where('user_id', $user->id)
            ->where('business_id', $businessId)
            ->where('document_name', $documentName)
            ->increment('questions_asked');
    }

    public function trackUserActivity(User $user, string $activityType, array $metadata = []): void
    {
        UserActivityAnalytic::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function getChatAnalytics(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $totalQuestions = ChatAnalytic::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $avgResponseTime = ChatAnalytic::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        $totalTokens = ChatAnalytic::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('tokens_used')
            ->sum('tokens_used');

        $dailyStats = ChatAnalytic::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as questions_count, AVG(response_time_ms) as avg_response_time')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_questions' => $totalQuestions,
            'avg_response_time_ms' => round($avgResponseTime ?? 0),
            'total_tokens_used' => $totalTokens,
            'daily_stats' => $dailyStats,
        ];
    }

    public function getDocumentAnalytics(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $documents = DocumentAnalytic::where('user_id', $user->id)
            ->with('knowledgeFile')
            ->orderBy('total_queries', 'desc')
            ->get();

        $totalDocuments = $documents->count();
        $totalQueries = $documents->sum('total_queries');
        $successfulQueries = $documents->sum('successful_queries');
        $failedQueries = $documents->sum('failed_queries');
        $totalSizeBytes = $documents->sum('file_size_bytes');
        $avgResponseTime = $documents->avg('avg_response_time_ms');

        // Get documents uploaded in the time period
        $recentDocuments = DocumentAnalytic::where('user_id', $user->id)
            ->where('uploaded_at', '>=', $startDate)
            ->count();

        // Get search terms analysis
        $allSearchTerms = $documents->pluck('search_terms')->filter()->flatten()->toArray();
        $searchTermsFrequency = array_count_values($allSearchTerms);
        arsort($searchTermsFrequency);

        // Calculate success rate
        $successRate = $totalQueries > 0 ? round(($successfulQueries / $totalQueries) * 100, 2) : 0;

        return [
            'total_documents' => $totalDocuments,
            'total_queries' => $totalQueries,
            'successful_queries' => $successfulQueries,
            'failed_queries' => $failedQueries,
            'success_rate' => $successRate,
            'avg_response_time_ms' => round($avgResponseTime ?? 0),
            'total_size_mb' => round($totalSizeBytes / 1024 / 1024, 2),
            'recent_uploads' => $recentDocuments,
            'most_used_documents' => $documents->take(10),
            'top_search_terms' => array_slice($searchTermsFrequency, 0, 20, true),
        ];
    }

    public function trackDocumentQuery(int $knowledgeFileId, string $searchTerm, bool $success = true, ?int $responseTimeMs = null): void
    {
        $documentAnalytic = DocumentAnalytic::where('knowledge_file_id', $knowledgeFileId)->first();

        if (! $documentAnalytic) {
            return;
        }

        // Update query counts
        $documentAnalytic->increment('total_queries');

        if ($success) {
            $documentAnalytic->increment('successful_queries');
        } else {
            $documentAnalytic->increment('failed_queries');
        }

        // Update average response time
        if ($responseTimeMs !== null) {
            $currentAvg = $documentAnalytic->avg_response_time_ms ?? 0;
            $currentQueries = $documentAnalytic->total_queries;
            $newAvg = (($currentAvg * ($currentQueries - 1)) + $responseTimeMs) / $currentQueries;
            $documentAnalytic->avg_response_time_ms = round($newAvg, 2);
        }

        // Track search terms
        $searchTerms = $documentAnalytic->search_terms ?? [];
        $searchTerms[] = $searchTerm;
        // Keep only last 100 search terms to prevent bloat
        $documentAnalytic->search_terms = array_slice($searchTerms, -100);

        // Update last accessed time
        $documentAnalytic->last_accessed_at = now();

        $documentAnalytic->save();
    }

    public function getDocumentPerformanceStats(User $user): array
    {
        $documents = DocumentAnalytic::where('user_id', $user->id)
            ->with('knowledgeFile')
            ->get();

        // Group by document type
        $typeStats = $documents->groupBy('document_type')->map(function ($docs, $type) {
            return [
                'type' => $type,
                'count' => $docs->count(),
                'total_queries' => $docs->sum('total_queries'),
                'avg_response_time' => round($docs->avg('avg_response_time_ms'), 2),
                'success_rate' => $docs->sum('total_queries') > 0
                    ? round(($docs->sum('successful_queries') / $docs->sum('total_queries')) * 100, 2)
                    : 0,
            ];
        })->values();

        // Get performance over time (last 7 days)
        $performanceTrend = DocumentAnalytic::where('user_id', $user->id)
            ->where('last_accessed_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(last_accessed_at) as date, 
                        COUNT(DISTINCT knowledge_file_id) as active_documents,
                        SUM(total_queries) as queries,
                        AVG(avg_response_time_ms) as avg_time')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'by_type' => $typeStats,
            'performance_trend' => $performanceTrend,
            'total_storage_mb' => round($documents->sum('file_size_bytes') / 1024 / 1024, 2),
        ];
    }

    public function getUserActivityStats(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $activities = UserActivityAnalytic::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('activity_type, COUNT(*) as count')
            ->groupBy('activity_type')
            ->get();

        return [
            'activity_breakdown' => $activities,
            'total_activities' => $activities->sum('count'),
        ];
    }

    // ===== ADMIN ANALYTICS METHODS =====

    /**
     * Get comprehensive platform analytics for admin dashboard
     */
    public function getPlatformAnalytics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'users' => $this->getUserMetrics($startDate),
            'revenue' => $this->getRevenueMetrics($startDate),
            'engagement' => $this->getEngagementMetrics($startDate),
            'growth' => $this->getGrowthMetrics($startDate),
            'usage' => $this->getUsageMetrics($startDate),
        ];
    }

    /**
     * Get user-related metrics
     */
    public function getUserMetrics(Carbon $startDate): array
    {
        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', $startDate)->count();
        $activeUsers = User::whereHas('chatAnalytics', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();

        // User plan distribution
        $planDistribution = User::join('plans', 'users.plan_id', '=', 'plans.id')
            ->selectRaw('plans.name, COUNT(*) as count')
            ->groupBy('plans.name')
            ->get();

        // User registration trend
        $registrationTrend = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_users' => $totalUsers,
            'new_users' => $newUsers,
            'active_users' => $activeUsers,
            'plan_distribution' => $planDistribution,
            'registration_trend' => $registrationTrend,
            'activation_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Get revenue-related metrics
     */
    public function getRevenueMetrics(Carbon $startDate): array
    {
        // Monthly Recurring Revenue (MRR)
        $mrr = User::join('plans', 'users.plan_id', '=', 'plans.id')
            ->whereNotNull('plans.price_cents')
            ->sum(DB::raw('plans.price_cents / 100'));

        // Revenue from orders
        $totalRevenue = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('amount') / 100;

        // Revenue trend
        $revenueTrend = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) / 100 as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Average Revenue Per User (ARPU)
        $payingUsers = User::whereHas('plan', function ($query) {
            $query->whereNotNull('price_cents')->where('price_cents', '>', 0);
        })->count();

        $arpu = $payingUsers > 0 ? $mrr / $payingUsers : 0;

        // Customer Lifetime Value (CLV) - simplified calculation
        $avgOrderValue = Order::where('status', 'paid')->avg('amount') / 100;
        $avgCustomerLifespan = 12; // months - this would be calculated from actual data
        $clv = $avgOrderValue * $avgCustomerLifespan;

        return [
            'mrr' => round($mrr, 2),
            'total_revenue' => round($totalRevenue, 2),
            'revenue_trend' => $revenueTrend,
            'arpu' => round($arpu, 2),
            'clv' => round($clv, 2),
            'paying_users' => $payingUsers,
        ];
    }

    /**
     * Get engagement metrics
     */
    public function getEngagementMetrics(Carbon $startDate): array
    {
        // Daily Active Users (DAU)
        $dau = User::whereHas('chatAnalytics', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();

        // Average session duration (simplified)
        $avgSessionDuration = UserActivityAnalytic::where('created_at', '>=', $startDate)
            ->where('activity_type', 'session_start')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, updated_at)'));

        // Feature adoption rates
        $featureAdoption = [
            'chat_usage' => User::whereHas('chatAnalytics', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'document_upload' => User::whereHas('documentAnalytics', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'personality_customization' => User::whereHas('userActivityAnalytics', function ($query) use ($startDate) {
                $query->where('activity_type', 'personality_created')->where('created_at', '>=', $startDate);
            })->count(),
        ];

        return [
            'dau' => $dau,
            'avg_session_duration' => round($avgSessionDuration ?? 0, 2),
            'feature_adoption' => $featureAdoption,
        ];
    }

    /**
     * Get growth metrics
     */
    public function getGrowthMetrics(Carbon $startDate): array
    {
        // User growth rate
        $previousPeriodUsers = User::where('created_at', '<', $startDate)->count();
        $currentPeriodUsers = User::where('created_at', '>=', $startDate)->count();
        $userGrowthRate = $previousPeriodUsers > 0 ?
            round((($currentPeriodUsers - $previousPeriodUsers) / $previousPeriodUsers) * 100, 2) : 0;

        // Revenue growth rate
        $previousRevenue = Order::where('status', 'paid')
            ->where('created_at', '<', $startDate)
            ->sum('amount') / 100;
        $currentRevenue = Order::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('amount') / 100;
        $revenueGrowthRate = $previousRevenue > 0 ?
            round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2) : 0;

        // Churn rate (simplified - users who haven't been active)
        $totalUsers = User::count();
        $inactiveUsers = User::whereDoesntHave('chatAnalytics', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();
        $churnRate = $totalUsers > 0 ? round(($inactiveUsers / $totalUsers) * 100, 2) : 0;

        return [
            'user_growth_rate' => $userGrowthRate,
            'revenue_growth_rate' => $revenueGrowthRate,
            'churn_rate' => $churnRate,
        ];
    }

    /**
     * Get usage metrics
     */
    public function getUsageMetrics(Carbon $startDate): array
    {
        // API usage
        $totalApiCalls = ChatAnalytic::where('created_at', '>=', $startDate)->count();
        $avgResponseTime = ChatAnalytic::where('created_at', '>=', $startDate)
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        // Document usage
        $totalDocuments = DocumentAnalytic::where('created_at', '>=', $startDate)->count();
        $totalDocumentQuestions = DocumentAnalytic::where('created_at', '>=', $startDate)
            ->sum('questions_asked');

        // Usage by plan
        $usageByPlan = User::join('plans', 'users.plan_id', '=', 'plans.id')
            ->join('chat_analytics', 'users.id', '=', 'chat_analytics.user_id')
            ->where('chat_analytics.created_at', '>=', $startDate)
            ->selectRaw('plans.name, COUNT(chat_analytics.id) as usage_count')
            ->groupBy('plans.name')
            ->get();

        return [
            'total_api_calls' => $totalApiCalls,
            'avg_response_time_ms' => round($avgResponseTime ?? 0),
            'total_documents' => $totalDocuments,
            'total_document_questions' => $totalDocumentQuestions,
            'usage_by_plan' => $usageByPlan,
        ];
    }

    /**
     * Get cohort analysis data
     */
    public function getCohortAnalysis(): array
    {
        $cohorts = User::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as cohort_month,
                COUNT(*) as users_count
            ')
            ->groupBy('cohort_month')
            ->orderBy('cohort_month')
            ->get();

        return [
            'cohorts' => $cohorts,
        ];
    }

    /**
     * Get top performing features
     */
    public function getTopFeatures(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $features = [
            'chat_interactions' => ChatAnalytic::where('created_at', '>=', $startDate)->count(),
            'document_uploads' => DocumentAnalytic::where('created_at', '>=', $startDate)->count(),
            'personality_creations' => UserActivityAnalytic::where('activity_type', 'personality_created')
                ->where('created_at', '>=', $startDate)->count(),
            'exports' => UserActivityAnalytic::where('activity_type', 'export_conversations')
                ->where('created_at', '>=', $startDate)->count(),
        ];

        return $features;
    }
}
