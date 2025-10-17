<?php

namespace App\Services;

use App\Models\TelegramBot;
use App\Models\TelegramBotCustomization;
use App\Models\TelegramBotAnalytic;
use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Services\ChatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnhancedTelegramService
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function processMessage(TelegramBot $bot, array $messageData): void
    {
        $chatId = $messageData['chat']['id'];
        $messageText = $messageData['text'] ?? '';
        $messageId = $messageData['message_id'];
        
        // Get or create chat record
        $chat = $this->getOrCreateChat($bot, $messageData['chat']);
        
        // Store the incoming message
        $this->storeMessage($chat, $messageData, false);
        
        // Get bot customization
        $customization = $bot->customization;
        
        // Check for quick replies first
        $quickReplyResponse = $this->checkQuickReplies($customization, $messageText);
        if ($quickReplyResponse) {
            $this->sendMessage($bot, $chatId, $quickReplyResponse, $messageId);
            return;
        }
        
        // Check for custom commands
        $commandResponse = $this->checkCommands($customization, $messageText);
        if ($commandResponse) {
            $this->sendMessage($bot, $chatId, $commandResponse, $messageId);
            return;
        }
        
        // Process with AI if it's a regular question
        if ($this->isQuestion($messageText)) {
            $this->processAIResponse($bot, $chatId, $messageText, $messageId, $customization);
        } else {
            // Send help message for non-questions
            $helpMessage = $customization?->getHelpMessage() ?? 'I can help you with questions about your documents. Just ask me anything!';
            $this->sendMessage($bot, $chatId, $helpMessage, $messageId);
        }
        
        // Update analytics
        $this->updateAnalytics($bot);
    }

    protected function getOrCreateChat(TelegramBot $bot, array $chatData): TelegramChat
    {
        return TelegramChat::firstOrCreate(
            [
                'telegram_bot_id' => $bot->id,
                'chat_id' => $chatData['id'],
            ],
            [
                'chat_type' => $chatData['type'] ?? 'private',
                'username' => $chatData['username'] ?? null,
                'first_name' => $chatData['first_name'] ?? null,
                'last_name' => $chatData['last_name'] ?? null,
            ]
        );
    }

    protected function storeMessage(TelegramChat $chat, array $messageData, bool $isFromBot): void
    {
        TelegramMessage::create([
            'telegram_chat_id' => $chat->id,
            'message_id' => $messageData['message_id'],
            'message_text' => $messageData['text'] ?? '',
            'is_from_bot' => $isFromBot,
            'telegram_timestamp' => now(),
        ]);
    }

    protected function checkQuickReplies(?TelegramBotCustomization $customization, string $messageText): ?string
    {
        if (!$customization) {
            return null;
        }

        $quickReplies = $customization->getQuickReplies();
        
        foreach ($quickReplies as $reply) {
            if (strtolower(trim($messageText)) === strtolower(trim($reply['text']))) {
                return $reply['response'];
            }
        }
        
        return null;
    }

    protected function checkCommands(?TelegramBotCustomization $customization, string $messageText): ?string
    {
        if (!$customization) {
            return null;
        }

        $commands = $customization->getCommands();
        
        foreach ($commands as $command) {
            $commandText = '/' . ltrim($command['command'], '/');
            if (strtolower(trim($messageText)) === strtolower($commandText)) {
                return $command['response'];
            }
        }
        
        return null;
    }

    protected function isQuestion(string $messageText): bool
    {
        $questionWords = ['what', 'how', 'when', 'where', 'why', 'who', 'which', 'can', 'could', 'would', 'should', 'is', 'are', 'do', 'does', 'did'];
        $messageLower = strtolower($messageText);
        
        foreach ($questionWords as $word) {
            if (strpos($messageLower, $word) !== false) {
                return true;
            }
        }
        
        return str_ends_with(trim($messageText), '?');
    }

    protected function processAIResponse(TelegramBot $bot, string $chatId, string $question, int $messageId, ?TelegramBotCustomization $customization): void
    {
        try {
            $startTime = microtime(true);
            
            // Get AI response using the existing ChatService
            $aiResponse = $this->chatService->getAnswer($question, $bot->business_id);
            
            // Apply personality customization
            $customizedResponse = $this->applyPersonalityCustomization($aiResponse, $customization);
            
            // Send the response
            $this->sendMessage($bot, $chatId, $customizedResponse, $messageId);
            
            // Calculate response time
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            // Update analytics
            $this->updateAnalytics($bot, $responseTime, true);
            
        } catch (\Exception $e) {
            Log::error('AI response error: ' . $e->getMessage());
            
            $errorMessage = $customization?->getErrorMessage() ?? 'Sorry, I encountered an error. Please try again.';
            $this->sendMessage($bot, $chatId, $errorMessage, $messageId);
            
            $this->updateAnalytics($bot, 0, false);
        }
    }

    protected function applyPersonalityCustomization(string $response, ?TelegramBotCustomization $customization): string
    {
        if (!$customization) {
            return $response;
        }

        $personalitySettings = $customization->personality_settings ?? [];
        $tone = $personalitySettings['tone'] ?? 'professional';
        $style = $personalitySettings['style'] ?? 'helpful';
        
        // Apply tone modifications
        switch ($tone) {
            case 'friendly':
                $response = $this->makeFriendly($response);
                break;
            case 'enthusiastic':
                $response = $this->makeEnthusiastic($response);
                break;
            case 'casual':
                $response = $this->makeCasual($response);
                break;
            case 'formal':
                $response = $this->makeFormal($response);
                break;
        }
        
        // Apply style modifications
        switch ($style) {
            case 'conversational':
                $response = $this->makeConversational($response);
                break;
            case 'direct':
                $response = $this->makeDirect($response);
                break;
            case 'supportive':
                $response = $this->makeSupportive($response);
                break;
        }
        
        return $response;
    }

    protected function makeFriendly(string $response): string
    {
        // Add friendly elements
        if (!str_starts_with($response, 'Hi') && !str_starts_with($response, 'Hello')) {
            $greetings = ['Hi there! ', 'Hello! ', 'Hey! '];
            $response = $greetings[array_rand($greetings)] . $response;
        }
        
        return $response;
    }

    protected function makeEnthusiastic(string $response): string
    {
        // Add enthusiasm
        $enthusiasticWords = ['amazing', 'fantastic', 'awesome', 'incredible', 'wonderful'];
        $response = str_replace('good', $enthusiasticWords[array_rand($enthusiasticWords)], $response);
        
        if (!str_ends_with($response, '!')) {
            $response .= '!';
        }
        
        return $response;
    }

    protected function makeCasual(string $response): string
    {
        // Make more casual
        $response = str_replace(['I can help you', 'I will help you'], 'I can help ya', $response);
        $response = str_replace(['you can', 'you will'], 'you can', $response);
        
        return $response;
    }

    protected function makeFormal(string $response): string
    {
        // Make more formal
        $response = str_replace(['I can help you', 'I\'ll help you'], 'I shall assist you', $response);
        $response = str_replace(['you can', 'you\'ll'], 'you may', $response);
        
        return $response;
    }

    protected function makeConversational(string $response): string
    {
        // Add conversational elements
        if (strlen($response) > 50) {
            $response = 'Well, ' . strtolower(substr($response, 0, 1)) . substr($response, 1);
        }
        
        return $response;
    }

    protected function makeDirect(string $response): string
    {
        // Make more direct
        $response = preg_replace('/^Well,?\s*/', '', $response);
        $response = preg_replace('/^So,?\s*/', '', $response);
        
        return $response;
    }

    protected function makeSupportive(string $response): string
    {
        // Add supportive elements
        $supportivePhrases = ['I understand', 'I\'m here to help', 'Don\'t worry'];
        $response = $supportivePhrases[array_rand($supportivePhrases)] . '. ' . $response;
        
        return $response;
    }

    protected function sendMessage(TelegramBot $bot, string $chatId, string $text, int $replyToMessageId = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
        
        if ($replyToMessageId) {
            $payload['reply_to_message_id'] = $replyToMessageId;
        }
        
        // Add keyboard if available
        $customization = $bot->customization;
        if ($customization && $customization->keyboard_layout) {
            $keyboard = $this->buildKeyboard($customization->keyboard_layout);
            if ($keyboard) {
                $payload['reply_markup'] = json_encode($keyboard);
            }
        }
        
        try {
            $response = Http::post("https://api.telegram.org/bot{$bot->bot_token}/sendMessage", $payload);
            
            if ($response->successful()) {
                Log::info('Message sent successfully', [
                    'bot_id' => $bot->id,
                    'chat_id' => $chatId,
                    'response' => $response->json(),
                ]);
            } else {
                Log::error('Failed to send message', [
                    'bot_id' => $bot->id,
                    'chat_id' => $chatId,
                    'error' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending message: ' . $e->getMessage());
        }
    }

    protected function buildKeyboard(array $keyboardLayout): ?array
    {
        if (empty($keyboardLayout)) {
            return null;
        }
        
        $keyboard = [];
        
        foreach ($keyboardLayout as $row) {
            $keyboardRow = [];
            foreach ($row['buttons'] as $button) {
                if (!empty($button['text'])) {
                    $keyboardRow[] = [
                        'text' => $button['text'],
                        'callback_data' => $button['value'] ?? $button['text'],
                    ];
                }
            }
            if (!empty($keyboardRow)) {
                $keyboard[] = $keyboardRow;
            }
        }
        
        return !empty($keyboard) ? ['inline_keyboard' => $keyboard] : null;
    }

    protected function updateAnalytics(TelegramBot $bot, float $responseTime = 0, bool $success = true): void
    {
        $today = now()->toDateString();
        
        $analytic = TelegramBotAnalytic::firstOrCreate(
            [
                'telegram_bot_id' => $bot->id,
                'date' => $today,
            ],
            [
                'total_messages' => 0,
                'ai_responses' => 0,
                'user_interactions' => 0,
                'new_users' => 0,
                'active_users' => 0,
                'avg_response_time' => 0,
                'successful_responses' => 0,
                'failed_responses' => 0,
            ]
        );
        
        $analytic->increment('total_messages');
        $analytic->increment('user_interactions');
        
        if ($responseTime > 0) {
            $analytic->increment('ai_responses');
            
            // Update average response time
            $totalResponses = $analytic->ai_responses;
            $currentAvg = $analytic->avg_response_time;
            $newAvg = (($currentAvg * ($totalResponses - 1)) + $responseTime) / $totalResponses;
            $analytic->update(['avg_response_time' => $newAvg]);
            
            if ($success) {
                $analytic->increment('successful_responses');
            } else {
                $analytic->increment('failed_responses');
            }
        }
    }
}