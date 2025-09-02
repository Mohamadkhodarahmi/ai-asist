<?php

namespace App\Services;

use App\Models\TelegramAnalytics;
use App\Models\TelegramBot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramAnalyticsService
{
    public function trackMessage(TelegramBot $bot, bool $isAiProcessed, float $responseTime = null, bool $isSuccessful = true): void
    {
        $today = Carbon::today();
        
        DB::transaction(function () use ($bot, $isAiProcessed, $responseTime, $isSuccessful, $today) {
            $analytics = TelegramAnalytics::firstOrCreate(
                [
                    'telegram_bot_id' => $bot->id,
                    'date' => $today,
                ],
                [
                    'total_messages' => 0,
                    'ai_processed_messages' => 0,
                    'average_response_time' => 0,
                    'active_users_count' => 0,
                    'successful_responses' => 0,
                    'failed_responses' => 0,
                ]
            );

            $analytics->increment('total_messages');
            
            if ($isAiProcessed) {
                $analytics->increment('ai_processed_messages');
            }

            if ($responseTime !== null) {
                $currentAvg = $analytics->average_response_time ?? 0;
                $totalMessages = $analytics->total_messages;
                $newAvg = (($currentAvg * ($totalMessages - 1)) + $responseTime) / $totalMessages;
                $analytics->average_response_time = $newAvg;
            }

            if ($isSuccessful) {
                $analytics->increment('successful_responses');
            } else {
                $analytics->increment('failed_responses');
            }

            $analytics->save();
        });
    }

    public function updateActiveUsers(TelegramBot $bot): void
    {
        $today = Carbon::today();
        
        $activeUsersCount = $bot->chats()
            ->whereHas('messages', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->count();

        TelegramAnalytics::updateOrCreate(
            [
                'telegram_bot_id' => $bot->id,
                'date' => $today,
            ],
            [
                'active_users_count' => $activeUsersCount,
            ]
        );
    }

    public function getBotMetrics(TelegramBot $bot, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = TelegramAnalytics::where('telegram_bot_id', $bot->id);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $metrics = $query->get();

        return [
            'total_messages' => $metrics->sum('total_messages'),
            'ai_processed_messages' => $metrics->sum('ai_processed_messages'),
            'average_response_time' => $metrics->avg('average_response_time'),
            'active_users' => $metrics->max('active_users_count'),
            'success_rate' => $metrics->sum('successful_responses') / max(1, $metrics->sum('total_messages')) * 100,
            'daily_metrics' => $metrics->map(fn($m) => [
                'date' => $m->date->toDateString(),
                'messages' => $m->total_messages,
                'ai_processed' => $m->ai_processed_messages,
                'response_time' => $m->average_response_time,
                'active_users' => $m->active_users_count,
            ]),
        ];
    }
}