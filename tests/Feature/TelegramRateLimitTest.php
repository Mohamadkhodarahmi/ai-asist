<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TelegramBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

class TelegramRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('telegram');
    }

    public function test_webhook_is_rate_limited()
    {
        $bot = TelegramBot::factory()->create(['is_active' => true]);
        
        // Make 11 requests (1 over the limit)
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson("/api/webhook/telegram/{$bot->bot_token}", [
                'update_id' => 123456789,
                'message' => [
                    'message_id' => 1,
                    'from' => [
                        'id' => 12345,
                        'username' => 'test_user'
                    ],
                    'chat' => [
                        'id' => 12345,
                        'type' => 'private',
                        'username' => 'test_user'
                    ],
                    'text' => 'Test message'
                ]
            ]);

            if ($i < 10) {
                $response->assertSuccessful();
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    public function test_rate_limit_resets_after_one_minute()
    {
        $bot = TelegramBot::factory()->create(['is_active' => true]);
        
        // Make 10 requests to hit the limit
        for ($i = 0; $i < 10; $i++) {
            $this->postJson("/api/webhook/telegram/{$bot->bot_token}", [
                'update_id' => 123456789,
                'message' => [
                    'message_id' => 1,
                    'from' => [
                        'id' => 12345,
                        'username' => 'test_user'
                    ],
                    'chat' => [
                        'id' => 12345,
                        'type' => 'private',
                        'username' => 'test_user'
                    ],
                    'text' => 'Test message'
                ]
            ]);
        }

        // Simulate waiting for 1 minute
        $this->travel(1)->minutes();

        // This request should now succeed
        $response = $this->postJson("/api/webhook/telegram/{$bot->bot_token}", [
            'update_id' => 123456789,
            'message' => [
                'message_id' => 1,
                'from' => [
                    'id' => 12345,
                    'username' => 'test_user'
                ],
                'chat' => [
                    'id' => 12345,
                    'type' => 'private',
                    'username' => 'test_user'
                ],
                'text' => 'Test message'
            ]
        ]);

        $response->assertSuccessful();
    }
}