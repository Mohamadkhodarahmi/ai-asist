<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeFile;
use App\Jobs\ProcessKnowledgeFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KnowledgeFileController extends Controller
{
    /**
     * Display a listing of the knowledge files for the user's business.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get the currently authenticated user's business
        $business = Auth::user()->business;

        // Fetch paginated knowledge files belonging to that business
        $files = $business->knowledgeFiles()->latest()->paginate(15);

        return response()->json($files);
    }

    /**
     * Store a newly uploaded knowledge file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request.
        $request->validate([
            'document' => [
                'required',
                'file',
                'mimes:pdf,txt,docx', // Allowed file types
                'max:10240', // Max file size in kilobytes (10MB)
            ],
        ]);

        // Get the authenticated user's business to associate the file with.
        $business = Auth::user()->business;
        if (!$business) {
            return response()->json(['message' => 'User is not associated with a business.'], 403);
        }

        // Store the file in a private directory.
        $path = $request->file('document')->store('knowledge_files');

        // Create a record in the database.
        $file = $business->knowledgeFiles()->create([
            'original_name' => $request->file('document')->getClientOriginalName(),
            'storage_path'  => $path,
            'status'        => 'pending', // Initial status
        ]);

        // Dispatch the job to process this file in the background.
        ProcessKnowledgeFile::dispatch($file);

        // Return a 202 Accepted response, indicating the request is accepted for processing.
        return response()->json([
            'message' => 'File uploaded successfully and is now being processed.',
            'file'    => $file,
        ], 202);
    }

    /**
     * Display the specified knowledge file.
     *
     * @param  \App\Models\KnowledgeFile  $knowledgeFile
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(KnowledgeFile $knowledgeFile): JsonResponse
    {
        // Authorize the action using the KnowledgeFilePolicy.
        // This will throw an exception if the user is not allowed to view this file.
        $this->authorize('view', $knowledgeFile);

        return response()->json($knowledgeFile);
    }

    /**
     * Remove the specified knowledge file from storage and database.
     *
     * @param  \App\Models\KnowledgeFile  $knowledgeFile
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(KnowledgeFile $knowledgeFile): JsonResponse
    {
        // Authorize the action using the KnowledgeFilePolicy.
        $this->authorize('delete', $knowledgeFile);

        // Delete the physical file from storage.
        Storage::delete($knowledgeFile->storage_path);

        // Delete the model record from the database.
        // Associated text_chunks will be deleted automatically if you set up cascading deletes.
        $knowledgeFile->delete();

        // Return a 204 No Content response, indicating success with no body.
        return response()->json(null, 204);
    }
}
