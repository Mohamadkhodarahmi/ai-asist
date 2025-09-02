<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_chat_id',
        'message_id',
        'message_text',
        'is_from_bot',
        'telegram_timestamp',
    ];

    protected $casts = [
        'is_from_bot' => 'boolean',
        'telegram_timestamp' => 'datetime',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegramChat::class, 'telegram_chat_id');
    }
}