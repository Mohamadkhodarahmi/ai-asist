<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users associated with the business.
     * A business can have multiple users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the knowledge files for the business.
     * A business can have multiple knowledge files.
     */
    public function knowledgeFiles(): HasMany
    {
        return $this->hasMany(KnowledgeFile::class);
    }

    /**
     * Get the telegram bots for the business.
     * A business can have multiple telegram bots.
     */
    public function telegramBots(): HasMany
    {
        return $this->hasMany(TelegramBot::class);
    }
}
