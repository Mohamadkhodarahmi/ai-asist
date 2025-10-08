<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Create a new subscription for a user
     */
    public function createSubscription(User $user, Plan $plan, array $options = []): Subscription
    {
        $trialDays = $options['trial_days'] ?? 0;
        $billingPeriod = $options['billing_period'] ?? 'monthly';

        $now = now();
        $trialEndsAt = $trialDays > 0 ? $now->copy()->addDays($trialDays) : null;
        $periodStart = $trialEndsAt ? $trialEndsAt : $now;
        $periodEnd = $this->calculatePeriodEnd($periodStart, $billingPeriod);

        $status = $trialDays > 0 ? 'trialing' : 'active';

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'billing_period' => $billingPeriod,
            'trial_ends_at' => $trialEndsAt,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'amount_cents' => $plan->price_cents,
            'provider' => 'nowpayments',
            'provider_subscription_id' => $options['provider_subscription_id'] ?? null,
            'metadata' => $options['metadata'] ?? [],
        ]);

        // Update user's plan
        $user->plan()->associate($plan);
        $user->save();

        Log::info('Subscription created', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'status' => $status,
        ]);

        return $subscription;
    }

    /**
     * Process subscription renewal
     */
    public function processRenewal(Subscription $subscription): bool
    {
        try {
            // Create new order for renewal
            $order = Order::create([
                'user_id' => $subscription->user_id,
                'plan_id' => $subscription->plan_id,
                'amount' => $subscription->amount_cents,
                'status' => 'pending',
                'provider' => $subscription->provider,
                'billing_period' => $subscription->billing_period,
                'billing_cycle_start' => $subscription->current_period_start,
                'billing_cycle_end' => $subscription->current_period_end,
                'is_recurring' => true,
                'subscription_id' => $subscription->provider_subscription_id,
                'metadata' => [
                    'renewal' => true,
                    'subscription_id' => $subscription->id,
                ],
            ]);

            // Update subscription period
            $newPeriodStart = $subscription->current_period_end;
            $newPeriodEnd = $this->calculatePeriodEnd($newPeriodStart, $subscription->billing_period);

            $subscription->update([
                'current_period_start' => $newPeriodStart,
                'current_period_end' => $newPeriodEnd,
                'status' => 'active',
            ]);

            Log::info('Subscription renewed', [
                'subscription_id' => $subscription->id,
                'order_id' => $order->id,
                'new_period_end' => $newPeriodEnd,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Subscription renewal failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Subscription $subscription, bool $immediate = false): void
    {
        if ($immediate) {
            $subscription->update([
                'status' => 'cancelled',
                'canceled_at' => now(),
                'ended_at' => now(),
            ]);

            // Downgrade user to free plan
            $freePlan = Plan::where('slug', 'free')->first();
            if ($freePlan) {
                $subscription->user->plan()->associate($freePlan);
                $subscription->user->save();
            }
        } else {
            // Cancel at period end
            $subscription->update([
                'status' => 'cancelled',
                'canceled_at' => now(),
            ]);
        }

        Log::info('Subscription cancelled', [
            'subscription_id' => $subscription->id,
            'immediate' => $immediate,
        ]);
    }

    /**
     * Resume a cancelled subscription
     */
    public function resumeSubscription(Subscription $subscription): void
    {
        $subscription->update([
            'status' => 'active',
            'canceled_at' => null,
        ]);

        Log::info('Subscription resumed', [
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Subscription $subscription, Plan $newPlan, bool $prorate = true): Subscription
    {
        $oldPlan = $subscription->plan;

        if ($prorate && $subscription->isActive()) {
            // Calculate prorated amount
            $proratedAmount = $subscription->calculateProratedAmount($newPlan);

            // Create adjustment order
            Order::create([
                'user_id' => $subscription->user_id,
                'plan_id' => $newPlan->id,
                'amount' => $proratedAmount,
                'status' => 'paid',
                'provider' => $subscription->provider,
                'billing_period' => $subscription->billing_period,
                'is_recurring' => false,
                'subscription_id' => $subscription->provider_subscription_id,
                'metadata' => [
                    'plan_change' => true,
                    'old_plan_id' => $oldPlan->id,
                    'prorated' => true,
                ],
            ]);
        }

        // Update subscription
        $subscription->update([
            'plan_id' => $newPlan->id,
            'amount_cents' => $newPlan->price_cents,
        ]);

        // Update user's plan
        $subscription->user->plan()->associate($newPlan);
        $subscription->user->save();

        Log::info('Subscription plan changed', [
            'subscription_id' => $subscription->id,
            'old_plan_id' => $oldPlan->id,
            'new_plan_id' => $newPlan->id,
            'prorated' => $prorate,
        ]);

        return $subscription;
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment(Subscription $subscription): void
    {
        $subscription->markPastDue();

        // Send notification to user
        // TODO: Implement notification system

        Log::warning('Subscription payment failed', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
        ]);
    }

    /**
     * Process dunning management
     */
    public function processDunningManagement(): void
    {
        // Find subscriptions that are past due
        $pastDueSubscriptions = Subscription::pastDue()
            ->where('current_period_end', '<', now()->subDays(3))
            ->get();

        foreach ($pastDueSubscriptions as $subscription) {
            $this->handleFailedPayment($subscription);
        }

        // Find subscriptions that should be cancelled due to non-payment
        $expiredSubscriptions = Subscription::pastDue()
            ->where('current_period_end', '<', now()->subDays(7))
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $this->cancelSubscription($subscription, true);
        }
    }

    /**
     * Get subscription metrics
     */
    public function getSubscriptionMetrics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::active()->count(),
            'trial_subscriptions' => Subscription::trial()->count(),
            'cancelled_subscriptions' => Subscription::cancelled()->count(),
            'past_due_subscriptions' => Subscription::pastDue()->count(),
            'new_subscriptions' => Subscription::where('created_at', '>=', $startDate)->count(),
            'churn_rate' => $this->calculateChurnRate($startDate),
            'mrr' => $this->calculateMRR(),
            'arr' => $this->calculateARR(),
        ];
    }

    /**
     * Calculate churn rate
     */
    private function calculateChurnRate(Carbon $startDate): float
    {
        $totalActive = Subscription::where('created_at', '<', $startDate)->count();
        $cancelled = Subscription::cancelled()
            ->where('canceled_at', '>=', $startDate)
            ->count();

        return $totalActive > 0 ? ($cancelled / $totalActive) * 100 : 0;
    }

    /**
     * Calculate Monthly Recurring Revenue
     */
    private function calculateMRR(): float
    {
        $monthlySubscriptions = Subscription::active()
            ->where('billing_period', 'monthly')
            ->sum('amount_cents');

        $yearlySubscriptions = Subscription::active()
            ->where('billing_period', 'yearly')
            ->sum('amount_cents');

        return ($monthlySubscriptions + ($yearlySubscriptions / 12)) / 100;
    }

    /**
     * Calculate Annual Recurring Revenue
     */
    private function calculateARR(): float
    {
        return $this->calculateMRR() * 12;
    }

    /**
     * Calculate period end date
     */
    private function calculatePeriodEnd(Carbon $startDate, string $billingPeriod): Carbon
    {
        return match ($billingPeriod) {
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };
    }

    /**
     * Extend trial period
     */
    public function extendTrial(Subscription $subscription, int $days): void
    {
        $subscription->extendTrial($days);

        Log::info('Trial extended', [
            'subscription_id' => $subscription->id,
            'days_added' => $days,
            'new_trial_end' => $subscription->trial_ends_at,
        ]);
    }

    /**
     * Get subscriptions expiring soon
     */
    public function getExpiringSubscriptions(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Subscription::active()
            ->where('current_period_end', '<=', now()->addDays($days))
            ->where('current_period_end', '>', now())
            ->get();
    }

    /**
     * Get trial subscriptions ending soon
     */
    public function getTrialEndingSoon(int $days = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Subscription::trial()
            ->where('trial_ends_at', '<=', now()->addDays($days))
            ->where('trial_ends_at', '>', now())
            ->get();
    }
}
