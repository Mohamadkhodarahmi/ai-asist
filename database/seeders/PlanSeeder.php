<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Free Plan - Enhanced
        Plan::query()->updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'daily_message_limit' => 10, // Increased from 5
                'price_cents' => 0,
                'features' => [
                    '10 messages/day',
                    '1 document upload',
                    'Basic AI responses',
                    'Community support',
                    'Basic analytics',
                ],
            ]
        );

        // Starter Plan - NEW
        Plan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'daily_message_limit' => 200,
                'price_cents' => 2999, // $29.99
                'features' => [
                    '200 messages/day',
                    '5 document uploads',
                    'Telegram integration',
                    'Custom AI personality',
                    'Email support',
                    'Export conversations',
                    'Document analytics',
                ],
            ]
        );

        // Pro Plan - Reduced price, enhanced features
        Plan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'daily_message_limit' => 1000, // Increased from 100
                'price_cents' => 7999, // Reduced from $99 to $79.99
                'features' => [
                    '1,000 messages/day',
                    'Unlimited documents',
                    'Priority processing',
                    'API access',
                    'Advanced analytics',
                    'Custom branding',
                    'Webhook support',
                    'Multi-language support',
                ],
            ]
        );

        // Business Plan - Renamed from Unlimited
        Plan::query()->updateOrCreate(
            ['slug' => 'business'],
            [
                'name' => 'Business',
                'daily_message_limit' => 0, // 0 = unlimited
                'price_cents' => 19999, // $199.99
                'features' => [
                    'Unlimited messages',
                    'White-label solution',
                    'Team management',
                    'Dedicated support',
                    'Custom integrations',
                    'Advanced security',
                    'SSO integration',
                    'Audit logs',
                ],
            ]
        );

        // Remove old unlimited plan if it exists
        Plan::where('slug', 'unlimited')->delete();
    }
}
