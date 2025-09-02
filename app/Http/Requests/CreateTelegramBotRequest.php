<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTelegramBotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'bot_token' => ['required', 'string', 'unique:telegram_bots'],
            'bot_username' => ['required', 'string'],
            'webhook_url' => ['required', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_id.required' => 'A business must be selected.',
            'business_id.exists' => 'The selected business is invalid.',
            'bot_token.required' => 'The Telegram bot token is required.',
            'bot_token.unique' => 'This bot token is already registered.',
            'bot_username.required' => 'The bot username is required.',
            'webhook_url.required' => 'The webhook URL is required.',
            'webhook_url.url' => 'The webhook URL must be a valid URL.',
        ];
    }
}