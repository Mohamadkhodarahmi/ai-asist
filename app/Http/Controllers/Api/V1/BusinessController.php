<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SetTelegramWebhook;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class BusinessController extends Controller
{
    /**
     * Store a new business (AI Assistant) for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,txt,docx|max:10240', // 10MB max
        ]);

        try {
            // Create the business
            $business = Business::create([
                'name' => $request->name,
            ]);

            // Associate the business with the user
            $user = $request->user();
            $user->business_id = $business->id;
            $user->save();

            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $path = $file->store('knowledge_files');

                // Create knowledge file record
                $business->knowledgeFiles()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'storage_path' => $path,
                    'status' => 'pending',
                ]);

                // Dispatch job to process the file
                \App\Jobs\ProcessKnowledgeFile::dispatch($business->knowledgeFiles()->latest()->first());
            }

            return redirect()->route('chat')->with('status', 'Assistant created successfully!');

        } catch (Throwable $e) {
            Log::error('Failed to create business: '.$e->getMessage());

            return back()->withErrors(['business' => 'Failed to create assistant. Please try again.']);
        }
    }

    /**
     * Update the Telegram bot token and dispatch webhook job.
     */
    public function updateTelegram(Request $request): RedirectResponse
    {
        $request->validate([
            'telegram_token' => [
                'required',
                'string',
                'regex:/^[0-9]{8,10}:[a-zA-Z0-9_-]{35}$/',
            ],
        ], [
            'telegram_token.regex' => 'The token format is invalid.',
        ]);

        $business = $request->user()->business;

        if (! $business) {
            return redirect()->route('chat')->withErrors([
                'telegram' => 'You must create an assistant first.',
            ]);
        }

        $token = $request->telegram_token;

        try {
            // 1. Save the token in the database
            $business->update(['telegram_token' => $token]);

            // 2. Dispatch a job to set the Telegram webhook asynchronously
            SetTelegramWebhook::dispatch($business);

        } catch (Throwable $e) {
            Log::error('Failed to update Telegram token: '.$e->getMessage());

            return back()->withErrors([
                'telegram_token' => 'Connection failed. Please check your token and try again.',
            ]);
        }

        return redirect()->route('chat')->with('status', 'Telegram bot connected successfully!');
    }
}
