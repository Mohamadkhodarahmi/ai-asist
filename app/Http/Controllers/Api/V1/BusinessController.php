<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Business; // Make sure you have a Business model

class BusinessController extends Controller
{
    /**
     * Store a new business (AI Assistant) for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:10240'], // 10MB Max
        ]);

        // 1. Handle the file upload.
        // In a real application, you'd store this on a cloud disk like S3
        // and likely trigger a background job to process the document.
        $path = $request->file('document')->store('documents', 'public');

        // 2. Create the business record associated with the user.
        $request->user()->business()->create([
            'name' => $request->name,
            'document_path' => $path,
        ]);

        // 3. Redirect back to the chat page. The user will now see the chat interface.
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
