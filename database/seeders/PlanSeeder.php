<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'daily_message_limit' => 5,
                'price_cents' => 0,
                'features' => ['5 messages/day', 'Basic access'],
            ]
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'daily_message_limit' => 100,
                'price_cents' => 9900,
                'features' => ['100 messages/day', 'Priority processing'],
            ]
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'unlimited'],
            [
                'name' => 'Unlimited',
                'daily_message_limit' => 0, // 0 = unlimited
                'price_cents' => 19900,
                'features' => ['Unlimited messages', 'Priority support'],
            ]
        );
    }
}


