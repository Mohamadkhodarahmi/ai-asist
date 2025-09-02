<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Business;
use App\Models\TelegramBot;
use App\Models\TelegramAnalytics;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramAnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Business $business;
    private TelegramBot $bot;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->business = Business::factory()->create();
        $this->user = User::factory()->create(['business_id' => $this->business->id]);
        $this->bot = TelegramBot::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_can_get_bot_metrics()
    {
        TelegramAnalytics::create([
            'telegram_bot_id' => $this->bot->id,
            'total_messages' => 100,
            'ai_processed_messages' => 80,
            'average_response_time' => 0.75,
            'active_users_count' => 25,
            'successful_responses' => 70,
            'failed_responses' => 30,
            'date' => Carbon::today(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/telegram-bots/{$this->bot->id}/analytics?" . http_build_query([
                'start_date' => Carbon::now()->subDays(7)->toDateString(),
                'end_date' => Carbon::now()->toDateString(),
            ]));

        $response->assertOk()
            ->assertJsonStructure([
                'total_messages',
                'ai_processed_messages',
                'average_response_time',
                'active_users',
                'success_rate',
                'daily_metrics'
            ]);
    }

    public function test_cannot_access_other_business_bot_metrics()
    {
        $otherBusiness = Business::factory()->create();
        $otherBot = TelegramBot::factory()->create(['business_id' => $otherBusiness->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/telegram-bots/{$otherBot->id}/analytics");

        $response->assertForbidden();
    }

    public function test_can_get_dashboard_stats()
    {
        TelegramAnalytics::create([
            'telegram_bot_id' => $this->bot->id,
            'total_messages' => 50,
            'ai_processed_messages' => 40,
            'average_response_time' => 0.5,
            'active_users_count' => 10,
            'successful_responses' => 45,
            'failed_responses' => 5,
            'date' => Carbon::today(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/telegram/dashboard-stats');

        $response->assertOk()
            ->assertJsonStructure([
                'bots' => [
                    '*' => [
                        'bot_id',
                        'bot_username',
                        'today_metrics' => [
                            'total_messages',
                            'ai_processed_messages',
                            'average_response_time',
                            'active_users',
                            'success_rate',
                            'daily_metrics'
                        ]
                    ]
                ]
            ]);

        $firstBot = $response->json('bots.0');
        $this->assertEquals($this->bot->id, $firstBot['bot_id']);
        $this->assertEquals(50, $firstBot['today_metrics']['total_messages']);
    }
}