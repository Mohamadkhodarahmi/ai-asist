<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'message_text' => $this->message_text,
            'is_from_bot' => $this->is_from_bot,
            'telegram_timestamp' => $this->telegram_timestamp,
            'created_at' => $this->created_at,
            'chat' => [
                'id' => $this->chat->id,
                'username' => $this->chat->username,
                'first_name' => $this->chat->first_name,
                'last_name' => $this->chat->last_name,
            ],
        ];
    }
}