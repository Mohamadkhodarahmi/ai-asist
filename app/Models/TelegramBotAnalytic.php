<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramBotAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_bot_id',
        'date',
        'total_messages',
        'ai_responses',
        'user_interactions',
        'new_users',
        'active_users',
        'avg_response_time',
        'successful_responses',
        'failed_responses',
        'popular_commands',
        'user_feedback',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'avg_response_time' => 'decimal:2',
            'popular_commands' => 'array',
            'user_feedback' => 'array',
        ];
    }

    public function telegramBot(): BelongsTo
    {
        return $this->belongsTo(TelegramBot::class);
    }

    public function getSuccessRate(): float
    {
        $total = $this->successful_responses + $this->failed_responses;
        if ($total === 0) {
            return 0;
        }
        
        return round(($this->successful_responses / $total) * 100, 2);
    }

    public function getResponseRate(): float
    {
        if ($this->total_messages === 0) {
            return 0;
        }
        
        return round(($this->ai_responses / $this->total_messages) * 100, 2);
    }

    public function getAvgResponseTimeFormatted(): string
    {
        if ($this->avg_response_time < 1000) {
            return round($this->avg_response_time) . 'ms';
        }
        
        return round($this->avg_response_time / 1000, 2) . 's';
    }
}