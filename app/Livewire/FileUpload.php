<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;

class FileUpload extends Component
{
    use WithFileUploads;

    public $document;
    public Business $business;

    public function mount()
    {
        // Load the business and its files for the authenticated user.
        \Log::info("FileUpload component mounted!");
        $this->business = Auth::user()->business()->with('knowledgeFiles')->first();
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
            'storage_path'  => $path,
            'status'        => 'pending',
        ]);

        // Dispatch the job to process the file in the background.
        \App\Jobs\ProcessKnowledgeFile::dispatch($this->business->knowledgeFiles()->latest()->first());

        // Reset the component state.
        $this->reset('document');
        $this->business = Auth::user()->business()->with('knowledgeFiles')->first();

        // Send a success message to the UI.
        session()->flash('message', 'File uploaded successfully and is now processing.');
    }

    public function render()
    {
        return view('livewire.file-upload')->extends('layouts.app');
    }
}
