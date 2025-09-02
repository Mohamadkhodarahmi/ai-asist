<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramBotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'bot_username' => $this->bot_username,
            'webhook_url' => $this->webhook_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'chats_count' => $this->when($this->chats_count !== null, $this->chats_count),
            'messages_count' => $this->when($this->messages_count !== null, $this->messages_count),
            'last_message_at' => $this->when($this->last_message_at !== null, $this->last_message_at),
        ];
    }
}