<?php

namespace App\Livewire;

use App\Models\TelegramBot;
use App\Models\TelegramBotAnalytic;
use App\Models\TelegramBotCustomization;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class TelegramBotAnalytics extends Component
{
    public ?TelegramBot $bot = null;
    public array $analytics = [];
    public array $chartData = [];
    public string $dateRange = '7'; // days
    public array $dateRanges = [
        '7' => 'Last 7 days',
        '30' => 'Last 30 days',
        '90' => 'Last 90 days',
    ];

    public function mount(?int $botId = null)
    {
        $this->loadBot($botId);
        $this->loadAnalytics();
    }

    protected function loadBot(?int $botId): void
    {
        if ($botId) {
            $this->bot = TelegramBot::where('business_id', Auth::user()->business_id)
                ->findOrFail($botId);
        } else {
            $this->bot = TelegramBot::where('business_id', Auth::user()->business_id)->first();
        }
    }

    public function loadAnalytics(): void
    {
        if (!$this->bot) {
            return;
        }

        $days = (int) $this->dateRange;
        $startDate = now()->subDays($days);

        // Get analytics data
        $analyticsData = TelegramBotAnalytic::where('telegram_bot_id', $this->bot->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get();

        // Calculate totals
        $this->analytics = [
            'total_messages' => $analyticsData->sum('total_messages'),
            'ai_responses' => $analyticsData->sum('ai_responses'),
            'user_interactions' => $analyticsData->sum('user_interactions'),
            'new_users' => $analyticsData->sum('new_users'),
            'avg_response_time' => $analyticsData->avg('avg_response_time') ?? 0,
            'success_rate' => $this->calculateSuccessRate($analyticsData),
            'response_rate' => $this->calculateResponseRate($analyticsData),
        ];

        // Prepare chart data
        $this->chartData = [
            'messages' => $this->prepareChartData($analyticsData, 'total_messages'),
            'ai_responses' => $this->prepareChartData($analyticsData, 'ai_responses'),
            'users' => $this->prepareChartData($analyticsData, 'active_users'),
            'response_time' => $this->prepareChartData($analyticsData, 'avg_response_time'),
        ];
    }

    protected function calculateSuccessRate($analyticsData): float
    {
        $totalResponses = $analyticsData->sum('successful_responses') + $analyticsData->sum('failed_responses');
        if ($totalResponses === 0) {
            return 0;
        }
        
        return round(($analyticsData->sum('successful_responses') / $totalResponses) * 100, 2);
    }

    protected function calculateResponseRate($analyticsData): float
    {
        $totalMessages = $analyticsData->sum('total_messages');
        if ($totalMessages === 0) {
            return 0;
        }
        
        return round(($analyticsData->sum('ai_responses') / $totalMessages) * 100, 2);
    }

    protected function prepareChartData($analyticsData, string $field): array
    {
        $data = [];
        $labels = [];
        
        foreach ($analyticsData as $analytic) {
            $labels[] = $analytic->date->format('M d');
            $data[] = $analytic->$field;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function updatedDateRange(): void
    {
        $this->loadAnalytics();
    }

    public function getBotCustomization(): ?TelegramBotCustomization
    {
        return $this->bot?->customization;
    }

    public function getBotStats(): array
    {
        if (!$this->bot) {
            return [];
        }

        $customization = $this->bot->customization;
        
        return [
            'bot_name' => $customization?->bot_name ?? 'Unnamed Bot',
            'is_active' => $this->bot->is_active,
            'total_chats' => $this->bot->chats()->count(),
            'total_messages' => $this->bot->messages()->count(),
            'personality_tone' => $customization?->getPersonalityTone() ?? 'professional',
            'personality_style' => $customization?->getPersonalityStyle() ?? 'helpful',
            'language' => $customization?->language ?? 'en',
            'commands_count' => count($customization?->getCommands() ?? []),
            'quick_replies_count' => count($customization?->getQuickReplies() ?? []),
        ];
    }

    public function getTopCommands(): array
    {
        if (!$this->bot) {
            return [];
        }

        $analyticsData = TelegramBotAnalytic::where('telegram_bot_id', $this->bot->id)
            ->where('date', '>=', now()->subDays(30))
            ->get();

        $commands = [];
        foreach ($analyticsData as $analytic) {
            $popularCommands = $analytic->popular_commands ?? [];
            foreach ($popularCommands as $command => $count) {
                $commands[$command] = ($commands[$command] ?? 0) + $count;
            }
        }

        arsort($commands);
        return array_slice($commands, 0, 5, true);
    }

    public function getRecentActivity(): array
    {
        if (!$this->bot) {
            return [];
        }

        return $this->bot->messages()
            ->with('telegramChat')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'text' => substr($message->message_text, 0, 100) . (strlen($message->message_text) > 100 ? '...' : ''),
                    'is_from_bot' => $message->is_from_bot,
                    'chat_username' => $message->telegramChat->username ?? 'Unknown',
                    'created_at' => $message->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.telegram-bot-analytics', [
            'botStats' => $this->getBotStats(),
            'topCommands' => $this->getTopCommands(),
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }
}