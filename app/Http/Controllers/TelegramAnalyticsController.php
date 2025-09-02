<?php

namespace App\Http\Controllers;

use App\Models\TelegramBot;
use App\Services\TelegramAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TelegramAnalyticsController extends Controller
{
    public function __construct(
        private TelegramAnalyticsService $analyticsService
    ) {}

    public function getMetrics(Request $request, TelegramBot $bot)
    {
        $this->authorize('view', $bot);

        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        return response()->json(
            $this->analyticsService->getBotMetrics($bot, $startDate, $endDate)
        );
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