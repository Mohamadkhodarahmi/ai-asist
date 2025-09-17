<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class TelegramService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Set the webhook URL for the bot.
     *
     * @param string $url The public URL for your webhook endpoint.
     * @return \Illuminate\Http\Client\Response
     */
    public function setWebhook(string $url): Response
    {
        return Http::post("{$this->baseUrl}/setWebhook", [
            'url' => $url,
        ]);
    }

    /**
     * Send a message to a specific chat.
     *
     * @param int $chatId The ID of the chat to send the message to.
     * @param string $text The message text.
     * @return \Illuminate\Http\Client\Response
     */
    public function sendMessage(int $chatId, string $text): Response
    {
        return Http::post("{$this->baseUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}