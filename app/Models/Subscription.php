<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'billing_period',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ended_at',
        'amount_cents',
        'provider',
        'provider_subscription_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'canceled_at' => 'datetime',
            'ended_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'subscription_id', 'provider_subscription_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->current_period_end->isFuture();
    }

    /**
     * Check if subscription is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->status === 'trialing' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || $this->canceled_at !== null;
    }

    /**
     * Check if subscription is past due
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Get days until next billing
     */
    public function getDaysUntilNextBilling(): int
    {
        return max(0, $this->current_period_end->diffInDays(now()));
    }

    /**
     * Calculate next billing date
     */
    public function getNextBillingDate(): Carbon
    {
        return $this->current_period_end->copy();
    }

    /**
     * Cancel the subscription
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'canceled_at' => now(),
        ]);
    }

    /**
     * Resume a cancelled subscription
     */
    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'canceled_at' => null,
        ]);
    }

    /**
     * Mark subscription as past due
     */
    public function markPastDue(): void
    {
        $this->update(['status' => 'past_due']);
    }

    /**
     * End the subscription
     */
    public function end(): void
    {
        $this->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);
    }

    /**
     * Extend trial period
     */
    public function extendTrial(int $days): void
    {
        $newTrialEnd = $this->trial_ends_at ?
            $this->trial_ends_at->addDays($days) :
            now()->addDays($days);

        $this->update(['trial_ends_at' => $newTrialEnd]);
    }

    /**
     * Calculate prorated amount for plan changes
     */
    public function calculateProratedAmount(Plan $newPlan): int
    {
        $daysRemaining = $this->current_period_end->diffInDays(now());
        $totalDays = $this->current_period_start->diffInDays($this->current_period_end);

        if ($totalDays <= 0) {
            return $newPlan->price_cents;
        }

        $proratedAmount = ($daysRemaining / $totalDays) * $newPlan->price_cents;

        return (int) round($proratedAmount);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('current_period_end', '>', now());
    }

    /**
     * Scope for trial subscriptions
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trialing')
            ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope for cancelled subscriptions
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for past due subscriptions
     */
    public function scopePastDue($query)
    {
        return $query->where('status', 'past_due');
    }
}
