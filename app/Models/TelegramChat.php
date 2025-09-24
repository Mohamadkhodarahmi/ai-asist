<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramChat extends Model
{
    use HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     * We set this to false because the ID comes from Telegram.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'telegram_bot_id',
        'chat_id',
        'chat_type',
        'username',
        'first_name',
        'last_name',
    ];

    /**
     * Get the business (bot) that this chat belongs to.
     */
    public function business(): BelongsTo
    {
        // Assuming your Business model holds the bot info
        // and its foreign key is telegram_bot_id
        return $this->belongsTo(Business::class, 'telegram_bot_id');
    }
}
