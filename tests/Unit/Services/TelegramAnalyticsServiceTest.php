<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\TelegramBot;
use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramAnalytics;
use App\Services\TelegramAnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private TelegramAnalyticsService $analyticsService;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyticsService = new TelegramAnalyticsService();
        $this->bot = TelegramBot::factory()->create();
    }

    public function test_track_message_creates_analytics_record()
    {
        $this->analyticsService->trackMessage($this->bot, true, 0.5, true);

        $this->assertDatabaseHas('telegram_analytics', [
            'telegram_bot_id' => $this->bot->id,
            'total_messages' => 1,
            'ai_processed_messages' => 1,
            'successful_responses' => 1,
            'failed_responses' => 0,
        ]);
    }

    public function test_track_message_updates_existing_analytics()
    {
        // Create initial analytics
        $this->analyticsService->trackMessage($this->bot, true, 0.5, true);
        
        // Track another message
        $this->analyticsService->trackMessage($this->bot, true, 1.5, true);

        $analytics = TelegramAnalytics::where('telegram_bot_id', $this->bot->id)
            ->where('date', Carbon::today())
            ->first();

        $this->assertEquals(2, $analytics->total_messages);
        $this->assertEquals(2, $analytics->ai_processed_messages);
        $this->assertEquals(1.0, $analytics->average_response_time);
    }

    public function test_update_active_users_counts_unique_users()
    {
        $chat1 = TelegramChat::factory()->create(['telegram_bot_id' => $this->bot->id]);
        $chat2 = TelegramChat::factory()->create(['telegram_bot_id' => $this->bot->id]);

        // Create messages for today
        TelegramMessage::factory()->create([
            'telegram_chat_id' => $chat1->id,
            'created_at' => now()
        ]);
        TelegramMessage::factory()->create([
            'telegram_chat_id' => $chat2->id,
            'created_at' => now()
        ]);

        $this->analyticsService->updateActiveUsers($this->bot);

        $analytics = TelegramAnalytics::where('telegram_bot_id', $this->bot->id)
            ->where('date', Carbon::today())
            ->first();

        $this->assertEquals(2, $analytics->active_users_count);
    }

    public function test_get_bot_metrics_returns_correct_data()
    {
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        // Create some analytics records
        TelegramAnalytics::create([
            'telegram_bot_id' => $this->bot->id,
            'total_messages' => 10,
            'ai_processed_messages' => 8,
            'average_response_time' => 0.5,
            'active_users_count' => 3,
            'successful_responses' => 7,
            'failed_responses' => 3,
            'date' => Carbon::today(),
        ]);

        $metrics = $this->analyticsService->getBotMetrics($this->bot, $startDate, $endDate);

        $this->assertArrayHasKey('total_messages', $metrics);
        $this->assertArrayHasKey('ai_processed_messages', $metrics);
        $this->assertArrayHasKey('average_response_time', $metrics);
        $this->assertArrayHasKey('active_users', $metrics);
        $this->assertArrayHasKey('success_rate', $metrics);
        $this->assertArrayHasKey('daily_metrics', $metrics);

        $this->assertEquals(10, $metrics['total_messages']);
        $this->assertEquals(8, $metrics['ai_processed_messages']);
        $this->assertEquals(0.5, $metrics['average_response_time']);
        $this->assertEquals(3, $metrics['active_users']);
        $this->assertEquals(70, $metrics['success_rate']);
    }
}