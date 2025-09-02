<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\TelegramBot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramBotFactory extends Factory
{
    protected $model = TelegramBot::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'bot_token' => $this->faker->regexify('[0-9]{10}:[a-zA-Z0-9]{35}'),
            'bot_username' => $this->faker->userName() . '_bot',
            'webhook_url' => $this->faker->url(),
            'is_active' => true,
        ];
    }
}