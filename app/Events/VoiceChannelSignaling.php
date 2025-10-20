<?php

namespace App\Events;

use App\Models\VoiceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceChannelSignaling implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public VoiceChannel $voiceChannel,
        public array $signalingData
    ) {}

    public function broadcastOn(): array
    {
        // For test-beep messages, broadcast to all participants in the voice channel
        if (isset($this->signalingData['type']) && $this->signalingData['type'] === 'test-beep') {
            return [
                new PrivateChannel('voice-channel.'.$this->voiceChannel->id),
                new PrivateChannel('group.'.$this->voiceChannel->group_id),
            ];
        }

        // For regular signaling, broadcast to specific user
        return [
            new PrivateChannel('voice-channel.'.$this->voiceChannel->id.'.user.'.$this->signalingData['target_user_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'VoiceChannelSignaling';
    }

    public function broadcastWith(): array
    {
        return $this->signalingData;
    }
}
