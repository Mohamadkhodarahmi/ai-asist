<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'name',
        'key',
        'permissions',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function rateLimits(): HasMany
    {
        return $this->hasMany(ApiRateLimit::class);
    }

    /**
     * Generate a new API key
     */
    public static function generateKey(): string
    {
        return 'ak_' . Str::random(48);
    }

    /**
     * Create a new API key for a user
     */
    public static function createForUser(User $user, string $name, ?array $permissions = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'business_id' => $user->business_id,
            'name' => $name,
            'key' => self::generateKey(),
            'permissions' => $permissions,
            'is_active' => true,
        ]);
    }

    /**
     * Check if the API key is valid and not expired
     */
    public function isValid(): bool
    {
        return $this->is_active && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Get the masked key for display purposes
     */
    public function getMaskedKeyAttribute(): string
    {
        return substr($this->key, 0, 8) . '...' . substr($this->key, -4);
    }
}