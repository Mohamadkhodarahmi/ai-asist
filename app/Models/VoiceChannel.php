<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoiceChannel extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'description',
        'is_active',
        'max_participants',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(VoiceSession::class);
    }

    public function activeSessions(): HasMany
    {
        return $this->hasMany(VoiceSession::class)->whereNull('left_at');
    }

    public function getParticipantsCountAttribute(): int
    {
        return $this->activeSessions()->count();
    }

    public function isFull(): bool
    {
        return $this->participants_count >= $this->max_participants;
    }

    public function hasUser(int $userId): bool
    {
        return $this->activeSessions()->where('user_id', $userId)->exists();
    }

    public function getUserSession(int $userId): ?VoiceSession
    {
        return $this->activeSessions()->where('user_id', $userId)->first();
    }
}
