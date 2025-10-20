<?php

namespace App\Events;

use App\Models\User;
use App\Models\VoiceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceChannelLeft implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public VoiceChannel $voiceChannel,
        public User $user
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('voice-channel.'.$this->voiceChannel->id),
            new PrivateChannel('group.'.$this->voiceChannel->group_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'VoiceChannelLeft';
    }

    public function broadcastWith(): array
    {
        return [
            'voice_channel_id' => $this->voiceChannel->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'left_at' => now()->toISOString(),
        ];
    }
}
