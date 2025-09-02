<?php

namespace Database\Factories;

use App\Models\TelegramBot;
use App\Models\TelegramAnalytics;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TelegramAnalyticsFactory extends Factory
{
    protected $model = TelegramAnalytics::class;

    public function definition(): array
    {
        return [
            'telegram_bot_id' => TelegramBot::factory(),
            'total_messages' => fake()->numberBetween(10, 1000),
            'ai_processed_messages' => fn (array $attrs) => fake()->numberBetween(1, $attrs['total_messages']),
            'average_response_time' => fake()->randomFloat(2, 0.1, 5.0),
            'active_users_count' => fake()->numberBetween(1, 100),
            'successful_responses' => fn (array $attrs) => fake()->numberBetween(1, $attrs['total_messages']),
            'failed_responses' => fn (array $attrs) => $attrs['total_messages'] - $attrs['successful_responses'],
            'date' => Carbon::today(),
        ];
    }

    public function highTraffic(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'total_messages' => fake()->numberBetween(1000, 10000),
                'active_users_count' => fake()->numberBetween(100, 1000),
            ];
        });
    }

    public function lowTraffic(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'total_messages' => fake()->numberBetween(1, 100),
                'active_users_count' => fake()->numberBetween(1, 10),
            ];
        });
    }
}