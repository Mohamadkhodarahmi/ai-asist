<?php

namespace App\Services;

use App\Models\ChatAnalytic;
use App\Models\DocumentAnalytic;
use App\Models\User;
use App\Models\UserActivityAnalytic;
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

    public function trackDocumentUpload(User $user, ?int $businessId, string $documentName, string $documentType, int $fileSizeBytes, ?int $pagesCount = null): void
    {
        DocumentAnalytic::updateOrCreate(
            [
                'user_id' => $user->id,
                'business_id' => $businessId,
                'document_name' => $documentName,
            ],
            [
                'document_type' => $documentType,
                'file_size_bytes' => $fileSizeBytes,
                'pages_count' => $pagesCount,
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

    public function getDocumentAnalytics(User $user): array
    {
        $documents = DocumentAnalytic::where('user_id', $user->id)
            ->orderBy('questions_asked', 'desc')
            ->get();

        $totalDocuments = $documents->count();
        $totalQuestions = $documents->sum('questions_asked');
        $totalSizeBytes = $documents->sum('file_size_bytes');

        return [
            'total_documents' => $totalDocuments,
            'total_questions' => $totalQuestions,
            'total_size_mb' => round($totalSizeBytes / 1024 / 1024, 2),
            'most_used_documents' => $documents->take(5),
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
}
