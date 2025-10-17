<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Free Plan - Reduced limits to encourage upgrades
        Plan::query()->updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'daily_message_limit' => 5, // Reduced from 10
                'price_cents' => 0,
                'features' => [
                    '5 messages/day',
                    '1 document upload',
                    'Basic AI responses',
                    'Community support',
                    '7-day premium trial',
                ],
            ]
        );

        // Starter Plan - NEW competitive pricing
        Plan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'daily_message_limit' => 100, // Reduced from 200
                'price_cents' => 1999, // Reduced from $29.99 to $19.99
                'features' => [
                    '100 messages/day',
                    '5 document uploads',
                    'Telegram bot integration',
                    'Custom AI personality',
                    'Email support',
                    'Export conversations',
                    'Basic analytics',
                ],
            ]
        );

        // Pro Plan - Enhanced features
        Plan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'daily_message_limit' => 500, // Reduced from 1000
                'price_cents' => 4999, // Reduced from $79.99 to $49.99
                'features' => [
                    '500 messages/day',
                    '25 document uploads',
                    'Advanced Telegram customization',
                    'Multiple bot personalities',
                    'Priority support',
                    'Advanced analytics',
                    'API access (1000 requests/month)',
                    'White-label options',
                ],
            ]
        );

        // Business Plan - Enterprise features
        Plan::query()->updateOrCreate(
            ['slug' => 'business'],
            [
                'name' => 'Business',
                'daily_message_limit' => 2000, // Increased from unlimited
                'price_cents' => 9999, // Reduced from $199.99 to $99.99
                'features' => [
                    '2,000 messages/day',
                    'Unlimited document uploads',
                    'Full Telegram bot builder',
                    'Multi-language support',
                    'Dedicated support',
                    'Custom integrations',
                    'API access (10,000 requests/month)',
                    'Team collaboration features',
                ],
            ]
        );

        // Enterprise Plan - NEW premium tier
        Plan::query()->updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'daily_message_limit' => 0, // 0 = unlimited
                'price_cents' => 29999, // $299.99
                'features' => [
                    'Unlimited messages',
                    'Unlimited everything',
                    'Custom AI model training',
                    'On-premise deployment option',
                    'SLA guarantee',
                    'Custom development',
                    'Advanced security features',
                    'Dedicated account manager',
                ],
            ]
        );

        // Remove old plans if they exist
        Plan::whereIn('slug', ['unlimited'])->delete();
    }
}
