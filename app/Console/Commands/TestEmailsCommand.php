<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Notifications\ExportReadyNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\SubscriptionChangedNotification;
use App\Notifications\UsageLimitWarningNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Console\Command;

class TestEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:test {email?} {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test transactional emails by sending them to a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $userId = $this->option('user');

        // Get user
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::query()->where('is_admin', true)->first() ?? User::first();
        }

        if (! $user) {
            $this->error('No user found. Please create a user first.');

            return self::FAILURE;
        }

        $this->info("Testing emails for user: {$user->name} ({$user->email})");
        $this->newLine();

        // If specific email type provided
        if ($email) {
            return $this->sendSpecificEmail($email, $user);
        }

        // Test all emails
        $choice = $this->choice(
            'Which email would you like to test?',
            [
                'all' => 'All Emails',
                'welcome' => 'Welcome Email',
                'payment' => 'Payment Receipt',
                'plan-upgrade' => 'Plan Upgrade',
                'plan-downgrade' => 'Plan Downgrade',
                'usage-warning' => 'Usage Limit Warning',
                'export-ready' => 'Export Ready',
            ],
            'all'
        );

        if ($choice === 'all' || $choice === 0 || $choice === 'All Emails') {
            return $this->sendAllEmails($user);
        }

        return $this->sendSpecificEmail($choice, $user);
    }

    /**
     * Send all test emails.
     */
    private function sendAllEmails(User $user): int
    {
        $this->info('Sending all test emails...');
        $this->newLine();

        $emails = [
            'welcome' => 'Welcome Email',
            'payment' => 'Payment Receipt',
            'plan-upgrade' => 'Plan Upgrade',
            'plan-downgrade' => 'Plan Downgrade',
            'usage-warning' => 'Usage Limit Warning',
            'export-ready' => 'Export Ready',
        ];

        foreach ($emails as $type => $name) {
            $this->sendSpecificEmail($type, $user);
            sleep(1); // Prevent rate limiting
        }

        return self::SUCCESS;
    }

    /**
     * Send a specific email type.
     */
    private function sendSpecificEmail(string $type, User $user): int
    {
        try {
            match ($type) {
                'welcome' => $this->sendWelcomeEmail($user),
                'payment' => $this->sendPaymentEmail($user),
                'plan-upgrade' => $this->sendPlanUpgradeEmail($user),
                'plan-downgrade' => $this->sendPlanDowngradeEmail($user),
                'usage-warning' => $this->sendUsageWarningEmail($user),
                'export-ready' => $this->sendExportReadyEmail($user),
                default => throw new \Exception("Unknown email type: {$type}"),
            };

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send {$type} email: ".$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Send welcome email.
     */
    private function sendWelcomeEmail(User $user): void
    {
        $this->info('📧 Sending Welcome Email...');
        $user->notify(new WelcomeNotification);
        $this->line('   ✓ Welcome email sent!');
        $this->newLine();
    }

    /**
     * Send payment email.
     */
    private function sendPaymentEmail(User $user): void
    {
        $this->info('💰 Sending Payment Receipt...');

        // Get or create a test order
        $order = Order::query()->where('user_id', $user->id)->first();

        if (! $order) {
            $this->warn('   No orders found. Creating test order...');
            $order = Order::create([
                'user_id' => $user->id,
                'plan_id' => $user->plan_id ?? 2,
                'amount_usd' => 29.00,
                'payment_method' => 'cryptocurrency',
                'status' => 'paid',
                'transaction_id' => 'test_'.uniqid(),
            ]);
        }

        $user->notify(new PaymentReceivedNotification($order));
        $this->line('   ✓ Payment receipt sent!');
        $this->newLine();
    }

    /**
     * Send plan upgrade email.
     */
    private function sendPlanUpgradeEmail(User $user): void
    {
        $this->info('🚀 Sending Plan Upgrade Email...');
        $user->notify(new SubscriptionChangedNotification(
            oldPlan: 'Free',
            newPlan: 'Pro',
            type: 'upgrade',
            features: [
                'Unlimited conversations',
                'Advanced analytics',
                'Priority support',
                'API access',
            ]
        ));
        $this->line('   ✓ Plan upgrade email sent!');
        $this->newLine();
    }

    /**
     * Send plan downgrade email.
     */
    private function sendPlanDowngradeEmail(User $user): void
    {
        $this->info('📉 Sending Plan Downgrade Email...');
        $user->notify(new SubscriptionChangedNotification(
            oldPlan: 'Pro',
            newPlan: 'Starter',
            type: 'downgrade',
            features: []
        ));
        $this->line('   ✓ Plan downgrade email sent!');
        $this->newLine();
    }

    /**
     * Send usage warning email.
     */
    private function sendUsageWarningEmail(User $user): void
    {
        $this->info('⚠️ Sending Usage Warning Email...');
        $user->notify(new UsageLimitWarningNotification(
            usageData: ['used' => 850, 'limit' => 1000],
            usagePercentage: 85,
            metrics: [
                'api_calls' => [
                    'name' => 'API Calls',
                    'used' => 850,
                    'limit' => 1000,
                    'percentage' => 85,
                ],
                'storage' => [
                    'name' => 'Storage',
                    'used' => 400,
                    'limit' => 500,
                    'percentage' => 80,
                ],
            ]
        ));
        $this->line('   ✓ Usage warning email sent!');
        $this->newLine();
    }

    /**
     * Send export ready email.
     */
    private function sendExportReadyEmail(User $user): void
    {
        $this->info('📦 Sending Export Ready Email...');
        $user->notify(new ExportReadyNotification(
            format: 'csv',
            downloadUrl: config('app.url').'/exports/download/test_'.uniqid(),
            dateFrom: '2025-01-01',
            dateTo: '2025-10-12',
            recordCount: 1547,
            fileSize: '2.5 MB'
        ));
        $this->line('   ✓ Export ready email sent!');
        $this->newLine();
    }
}
