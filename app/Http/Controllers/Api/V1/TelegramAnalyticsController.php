<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Services\TelegramAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TelegramAnalyticsController extends Controller
{
    public function __construct(
        private TelegramAnalyticsService $analyticsService
    ) {}

    public function getMetrics(Request $request, TelegramBot $telegramBot)
    {
        $this->authorize('view', $telegramBot);

        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(7);
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date) : Carbon::now();

        $metrics = $this->analyticsService->getBotMetrics($telegramBot, $startDate, $endDate);

        return response()->json($metrics);
    }

    public function getDashboardStats(Request $request)
    {
        $bots = TelegramBot::where('business_id', $request->user()->business_id)
            ->where('is_active', true)
            ->get();

        $stats = [];
        foreach ($bots as $bot) {
            $metrics = $this->analyticsService->getBotMetrics(
                $bot,
                Carbon::now()->startOfDay(),
                Carbon::now()
            );

            $stats[] = [
                'bot_id' => $bot->id,
                'bot_username' => $bot->bot_username,
                'today_metrics' => $metrics
            ];
        }

        return response()->json(['bots' => $stats]);
    }
}