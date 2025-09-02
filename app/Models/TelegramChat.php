<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_bot_id',
        'chat_id',
        'chat_type',
        'username',
        'first_name',
        'last_name',
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(TelegramBot::class, 'telegram_bot_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }
}