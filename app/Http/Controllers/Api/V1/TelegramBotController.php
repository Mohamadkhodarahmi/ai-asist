<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramChat;
use App\Services\TelegramService;
use App\Http\Requests\CreateTelegramBotRequest;
use App\Http\Resources\TelegramBotResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TelegramBotController extends Controller
{
    public function __construct(
        private TelegramService $telegramService
    ) {
        $this->authorizeResource(TelegramBot::class, 'telegram_bot');
    }

    public function index(Request $request)
    {
        $bots = TelegramBot::where('business_id', $request->business_id)
            ->withCount('chats', 'messages')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return TelegramBotResource::collection($bots);
    }

    public function store(CreateTelegramBotRequest $request)
    {
        $bot = TelegramBot::create($request->validated());

        $webhookRegistered = $this->telegramService->registerWebhook($bot);

        if (!$webhookRegistered) {
            $bot->delete();
            return response()->json([
                'message' => 'Failed to register webhook with Telegram'
            ], 500);
        }

        return new TelegramBotResource($bot);
    }

    public function show(TelegramBot $telegramBot)
    {
        return new TelegramBotResource(
            $telegramBot->load(['chats' => function($query) {
                $query->withCount('messages')
                    ->orderBy('updated_at', 'desc');
            }])
        );
    }

    public function toggleActivation(TelegramBot $telegramBot)
    {
        $this->authorize('update', $telegramBot);

        $telegramBot->is_active = !$telegramBot->is_active;
        $telegramBot->save();

        if ($telegramBot->is_active) {
            $this->telegramService->registerWebhook($telegramBot);
        }

        return new TelegramBotResource($telegramBot);
    }

    public function chatHistory(TelegramBot $telegramBot, TelegramChat $chat)
    {
        $this->authorize('view', $telegramBot);

        $messages = $chat->messages()
            ->orderBy('telegram_timestamp', 'desc')
            ->paginate(50);

        return response()->json([
            'chat' => $chat->only(['id', 'chat_id', 'username', 'first_name', 'last_name']),
            'messages' => $messages
        ]);
    }

    public function update(Request $request, TelegramBot $telegramBot)
    {
        $validator = Validator::make($request->all(), [
            'bot_token' => ['sometimes', 'string', Rule::unique('telegram_bots')->ignore($telegramBot->id)],
            'bot_username' => ['sometimes', 'string'],
            'webhook_url' => ['sometimes', 'url'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $telegramBot->update($validator->validated());

        if ($request->has('webhook_url') || $request->has('bot_token')) {
            $webhookRegistered = $this->telegramService->registerWebhook($telegramBot);
            if (!$webhookRegistered) {
                return response()->json([
                    'message' => 'Failed to update webhook with Telegram'
                ], 500);
            }
        }

        return new TelegramBotResource($telegramBot);
    }

    public function destroy(TelegramBot $telegramBot)
    {
        $telegramBot->delete();
        return response()->noContent();
    }
}