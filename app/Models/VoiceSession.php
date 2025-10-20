<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceSession extends Model
{
    protected $fillable = [
        'voice_channel_id',
        'user_id',
        'is_speaking',
        'is_muted',
        'is_deafened',
        'joined_at',
        'left_at',
        'audio_settings',
    ];

    protected $casts = [
        'is_speaking' => 'boolean',
        'is_muted' => 'boolean',
        'is_deafened' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'audio_settings' => 'array',
    ];

    public function voiceChannel(): BelongsTo
    {
        return $this->belongsTo(VoiceChannel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return is_null($this->left_at);
    }

    public function leave(): void
    {
        $this->update(['left_at' => now()]);
    }

    public function toggleMute(): void
    {
        $this->update(['is_muted' => ! $this->is_muted]);
    }

    public function toggleDeafen(): void
    {
        $this->update(['is_deafened' => ! $this->is_deafened]);
    }

    public function setSpeaking(bool $speaking): void
    {
        $this->update(['is_speaking' => $speaking]);
    }
}
