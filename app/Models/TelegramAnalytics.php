<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramAnalytics extends Model
{
    protected $fillable = [
        'telegram_bot_id',
        'total_messages',
        'ai_processed_messages',
        'average_response_time',
        'active_users_count',
        'successful_responses',
        'failed_responses',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'average_response_time' => 'float',
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }
}