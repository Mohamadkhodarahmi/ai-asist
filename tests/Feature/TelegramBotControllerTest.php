<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Business;
use App\Models\TelegramBot;
use App\Models\TelegramChat;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;

class TelegramBotControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->business = Business::factory()->create();
        $this->user = User::factory()->create(['business_id' => $this->business->id]);
    }

    public function test_can_create_telegram_bot()
    {
        $this->mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('registerWebhook')->once()->andReturn(true);
        });

        $response = $this->actingAs($this->user)->postJson('/api/v1/telegram-bots', [
            'business_id' => $this->business->id,
            'bot_token' => '1234567890:ABCdefGHIjklMNOpqrsTUVwxyz',
            'bot_username' => 'test_bot',
            'webhook_url' => 'https://example.com/webhook',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'business_id',
                    'bot_username',
                    'webhook_url',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_can_toggle_bot_activation()
    {
        $bot = TelegramBot::factory()->create([
            'business_id' => $this->business->id,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/telegram-bots/{$bot->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $bot->id,
                    'is_active' => false
                ]
            ]);

        $this->assertDatabaseHas('telegram_bots', [
            'id' => $bot->id,
            'is_active' => false
        ]);
    }

    public function test_can_view_chat_history()
    {
        $bot = TelegramBot::factory()->create([
            'business_id' => $this->business->id
        ]);

        $chat = TelegramChat::factory()
            ->create(['telegram_bot_id' => $bot->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/telegram-bots/{$bot->id}/chats/{$chat->id}/history");

        $response->assertOk()
            ->assertJsonStructure([
                'chat' => [
                    'id',
                    'chat_id',
                    'username',
                    'first_name',
                    'last_name'
                ],
                'messages' => [
                    'data',
                    'current_page',
                    'last_page'
                ]
            ]);
    }

    public function test_cannot_access_other_business_bots()
    {
        $otherBusiness = Business::factory()->create();
        $bot = TelegramBot::factory()->create([
            'business_id' => $otherBusiness->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/telegram-bots/{$bot->id}");

        $response->assertForbidden();
    }
}