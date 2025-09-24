<?php

namespace App\Livewire;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    public $document;

    public Business $business;

    public array $prompts = [];

    /**
     * Minimal presets to help non-technical users.
     *
     * @var array<string, string>
     */
    public array $promptPresets = [
        'friendly' => 'Use a friendly, warm, and encouraging tone.',
        'formal' => 'Use a professional and formal tone. Be precise.',
        'concise' => 'Answer concisely in 1-3 short sentences.',
    ];

    public function mount()
    {
        // Load the business and its files for the authenticated user.
        $this->business = Auth::user()->business()->with('knowledgeFiles')->first();
        $this->prompts = $this->business->knowledgeFiles->pluck('system_prompt', 'id')->toArray();
    }

    public function save()
    {
        // Validate the uploaded file.
        $this->validate([
            'document' => [
                'required',
                'file',
                'mimes:pdf,txt,docx',
                'max:10240', // 10MB Max
            ],
        ]);

        // Store the file in a private directory.
        $path = $this->document->store('knowledge_files');

        // Create a record in the database.
        $this->business->knowledgeFiles()->create([
            'original_name' => $this->document->getClientOriginalName(),
            'storage_path' => $path,
            'status' => 'pending',
        ]);

        // Dispatch the job to process the file in the background.
        \App\Jobs\ProcessKnowledgeFile::dispatch($this->business->knowledgeFiles()->latest()->first());

        // Reset the component state.
        $this->reset('document');
        $this->business = Auth::user()->business()->with('knowledgeFiles')->first();
        $this->prompts = $this->business->knowledgeFiles->pluck('system_prompt', 'id')->toArray();

        // Send a success message to the UI.
        session()->flash('message', 'File uploaded successfully and is now processing.');
    }

    public function savePrompt(int $fileId): void
    {
        $file = $this->business->knowledgeFiles()->whereKey($fileId)->firstOrFail();
        $prompt = $this->prompts[$fileId] ?? null;

        $this->validate([
            'prompts.'.$fileId => ['nullable', 'string', 'max:2000'],
        ]);

        $file->update([
            'system_prompt' => $prompt,
        ]);

        session()->flash('message', 'Prompt saved for '.$file->original_name.'.');
    }

    public function applyPreset(int $fileId, string $presetKey): void
    {
        if (! array_key_exists($presetKey, $this->promptPresets)) {
            return;
        }

        $this->prompts[$fileId] = $this->promptPresets[$presetKey];
    }

    public function render()
    {
        return view('livewire.file-upload')->layout('layouts.app');
    }
}
