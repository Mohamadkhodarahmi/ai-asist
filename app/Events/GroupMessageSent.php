<?php

namespace App\Events;

use App\Models\GroupMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GroupMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(GroupMessage $message)
    {
        $this->message = $message->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('group.'.$this->message->group_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'GroupMessageSent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'group_id' => $this->message->group_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user?->name,
            'message' => $this->message->message,
            'is_ai_response' => $this->message->is_ai_response,
            'created_at' => $this->message->created_at->toISOString(),
            'created_at_human' => $this->message->created_at->format('h:i A'),
        ];
    }
}
