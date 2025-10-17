<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the business that the user belongs to.
     * This defines the inverse of a one-to-many relationship.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function chatAnalytics(): HasMany
    {
        return $this->hasMany(ChatAnalytic::class);
    }

    public function documentAnalytics(): HasMany
    {
        return $this->hasMany(DocumentAnalytic::class);
    }

    public function userActivityAnalytics(): HasMany
    {
        return $this->hasMany(UserActivityAnalytic::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_id');
    }

    public function getReferralCode(): string
    {
        return 'USER' . strtoupper(substr(md5($this->id . $this->email), 0, 8));
    }

    public function getReferralLink(): string
    {
        return route('register') . '?ref=' . $this->getReferralCode();
    }

    public function getTotalReferralRewards(): int
    {
        return $this->referrals()
            ->where('status', 'completed')
            ->sum('reward_amount_cents');
    }

    public function getTotalReferralRewardsFormatted(): string
    {
        return '$' . number_format($this->getTotalReferralRewards() / 100, 2);
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
