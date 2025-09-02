<?php

namespace Database\Factories;

use App\Models\TelegramBot;
use App\Models\TelegramChat;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramChatFactory extends Factory
{
    protected $model = TelegramChat::class;

    public function definition(): array
    {
        return [
            'telegram_bot_id' => TelegramBot::factory(),
            'chat_id' => (string)fake()->numberBetween(1000000, 9999999),
            'chat_type' => fake()->randomElement(['private', 'group', 'supergroup']),
            'username' => fake()->userName(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];
    }
}