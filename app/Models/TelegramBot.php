<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'bot_token',
        'bot_username',
        'webhook_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(TelegramChat::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(TelegramWebhook::class);
    }
}