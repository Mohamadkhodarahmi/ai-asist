<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customization(): HasOne
    {
        return $this->hasOne(TelegramBotCustomization::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(TelegramBotAnalytic::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(TelegramChat::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(TelegramWebhook::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhook_url ?? route('telegram.bot', ['token' => $this->bot_token]);
    }

    public function getBotInfo(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->bot_username,
            'token' => $this->bot_token,
            'is_active' => $this->is_active,
            'webhook_url' => $this->getWebhookUrl(),
        ];
    }
}



