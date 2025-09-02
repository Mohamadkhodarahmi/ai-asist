<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramWebhook extends Model
{
    protected $fillable = [
        'telegram_bot_id',
        'payload',
        'event_type',
        'is_processed',
        'processing_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_processed' => 'boolean',
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}