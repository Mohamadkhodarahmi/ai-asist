<?php

namespace Database\Factories;

use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramMessageFactory extends Factory
{
    protected $model = TelegramMessage::class;

    public function definition(): array
    {
        return [
            'telegram_chat_id' => TelegramChat::factory(),
            'message_id' => fake()->numberBetween(1000, 999999),
            'message_text' => fake()->sentence(),
            'is_from_bot' => fake()->boolean(),
            'telegram_timestamp' => now(),
        ];
    }

    public function fromBot(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_from_bot' => true,
            ];
        });
    }

    public function fromUser(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_from_bot' => false,
            ];
        });
    }
}