<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AskChatRequest;
use App\Services\ChatService; // We will create this service next.
use App\Services\PlanLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Handle a request to ask a question to the AI assistant.
     *
     * @param \App\Http\Requests\Api\V1\AskChatRequest $request
     * @param \App\Services\ChatService $chatService
     * @return \Illuminate\Http\JsonResponse
     */
    public function ask(AskChatRequest $request, ChatService $chatService, PlanLimiter $planLimiter): JsonResponse
    {
        // The AskChatRequest class has already validated the incoming data.
        // We can safely access the validated data.
        $validatedData = $request->validated();

        try {
            $user = Auth::user();
            if ($user && ! $planLimiter->canSendMessage($user)) {
                return response()->json([
                    'message' => 'Daily message limit reached for your plan.'
                ], 429);
            }
            // The controller's job is simple: pass the data to the service
            // and let the service handle the complex logic.
            $answer = $chatService->getAnswer(
                $validatedData['question'],
                $validatedData['business_id']
            );

            // Count usage against plan limits when successful
            if ($user) {
                $planLimiter->hit($user);
            }

            // Return the answer from the service in a JSON response.
            return response()->json(['answer' => $answer]);

        } catch (\Exception $e) {
            // Log the exception for debugging purposes.
            // \Log::error($e->getMessage());

            // Return a generic server error response to the user.
            return response()->json([
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }
}
