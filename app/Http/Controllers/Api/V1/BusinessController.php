<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessKnowledgeFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Throwable;
use App\Models\Business; // Make sure you have a Business model

class BusinessController extends Controller
{
    /**
     * Store a new business (AI Assistant) for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validate both the name and the document
        $request->validate([
            'name' => 'required|string|max:255',
            'document' => [
                'required',
                'file',
                'mimes:pdf,txt,docx',
                'max:10240', // 10MB Max
            ],
        ]);

        $user = Auth::user();

        // Prevent creating a new business if one already exists
        if ($user->business) {
            return back()->withErrors(['name' => 'You already have an assistant.']);
        }

        try {
            // 2. Use a transaction to ensure data integrity
            DB::beginTransaction();

            // 3. Create the business
            $business = $user->business()->create([
                'name' => $request->name,
            ]);
            
            // Associate the business with the user model
            $user->business_id = $business->id;
            $user->save();

            // 4. Store the uploaded file
            $path = $request->file('document')->store('knowledge_files');

            // 5. Create the KnowledgeFile database record
            $file = $business->knowledgeFiles()->create([
                'original_name' => $request->file('document')->getClientOriginalName(),
                'storage_path'  => $path,
                'status'        => 'pending',
            ]);

            // 6. Dispatch a job to process the file in the background
            ProcessKnowledgeFile::dispatch($file);
            
            // If everything is successful, commit the changes
            DB::commit();

        } catch (Throwable $e) {
            // If any step fails, roll back all database changes
            DB::rollBack();
            // Optional: Log the error
            // Log::error('Failed to create assistant: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Could not create the assistant. Please try again.']);
        }

        // 7. Redirect to the chat interface
        return redirect()->route('chat')->with('status', 'Assistant created successfully!');
    }


    /**
     * Update the Telegram bot token for the user's business.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTelegram(Request $request): RedirectResponse
    {
        $request->validate([
            'telegram_token' => ['required', 'string', 'max:255'],
        ]);

        $business = $request->user()->business;

        if (!$business) {
            return redirect()->route('chat')->withErrors(['telegram' => 'You must create an assistant first.']);
        }

        $business->update([
            'telegram_token' => $request->telegram_token,
        ]);

        return redirect()->route('chat')->with('status', 'Telegram token updated!');
    }
}
