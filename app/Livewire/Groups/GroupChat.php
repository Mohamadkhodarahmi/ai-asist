<?php

namespace App\Livewire\Groups;

use App\Models\Business;
use App\Models\Group;
use App\Models\GroupFile;
use App\Models\GroupMessage;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class GroupChat extends Component
{
    use WithFileUploads;

    public Group $group;

    public $message = '';

    public $uploadFile;

    public $inviteEmail = '';

    public function mount(Group $group): void
    {
        // Check if user is a member
        if (! $group->members->contains(Auth::id())) {
            abort(403, 'You are not a member of this group');
        }

        $this->group = $group->load(['messages.user', 'files.knowledgeFile', 'members' => function ($query) {
            $query->withPivot('role');
        }]);
    }

    public function sendMessage(): void
    {
        \Log::info('GroupChat: sendMessage called', [
            'user_id' => Auth::id(),
            'group_id' => $this->group->id,
            'message_length' => strlen($this->message),
        ]);

        $this->validate([
            'message' => 'required|string|max:5000',
        ]);

        $userMessage = GroupMessage::create([
            'group_id' => $this->group->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_ai_response' => false,
        ]);

        \Log::info('GroupChat: User message created', [
            'message_id' => $userMessage->id,
            'message' => $userMessage->message,
        ]);

        // Broadcast the message directly (immediate)
        $broadcaster = app('Illuminate\Contracts\Broadcasting\Broadcaster');
        $event = new \App\Events\GroupMessageSent($userMessage);
        $broadcaster->broadcast($event->broadcastOn(), $event->broadcastAs(), $event->broadcastWith());

        $this->reset('message');
        $this->group->load(['messages.user', 'files.knowledgeFile', 'members' => function ($query) {
            $query->withPivot('role');
        }]);

        // Generate AI response only if AI is tagged
        if ($this->shouldGenerateAIResponse($userMessage->message)) {
            $this->generateAIResponse($userMessage);
        }
    }

    /**
     * Check if AI should respond to this message
     */
    protected function shouldGenerateAIResponse(string $message): bool
    {
        // Convert to lowercase for case-insensitive matching
        $message = strtolower($message);

        // List of AI trigger patterns
        $aiTriggers = [
            '@ai',
            '@assistant',
            '@bot',
            'hey ai',
            'ai,',
            'assistant,',
        ];

        // Check if any trigger pattern is found
        foreach ($aiTriggers as $trigger) {
            if (str_contains($message, $trigger)) {
                return true;
            }
        }

        return false;
    }

    protected function generateAIResponse(GroupMessage $userMessage): void
    {
        \Log::info('GroupChat: generateAIResponse started', [
            'user_message_id' => $userMessage->id,
            'question' => $userMessage->message,
        ]);

        try {
            $chatService = app(ChatService::class);

            // Get file IDs from this group
            $fileIds = $this->group->files->pluck('knowledge_file_id')->toArray();

            \Log::info('GroupChat: Files collected', [
                'file_count' => count($fileIds),
                'file_ids' => $fileIds,
            ]);

            if (empty($fileIds)) {
                $aiResponse = "I don't have any files to reference yet. Please upload some learning materials to the group first.";
                \Log::info('GroupChat: No files found, using default message');
            } else {
                // Use business_id from first file's business
                $businessId = $this->group->files->first()->knowledgeFile->business_id ?? Auth::user()->business_id;

                \Log::info('GroupChat: Calling ChatService', [
                    'business_id' => $businessId,
                    'question' => $userMessage->message,
                ]);

                $aiResponse = $chatService->getAnswer($userMessage->message, $businessId);

                \Log::info('GroupChat: AI response received', [
                    'response_length' => strlen($aiResponse),
                    'response_preview' => substr($aiResponse, 0, 100),
                ]);
            }

            $aiMessage = GroupMessage::create([
                'group_id' => $this->group->id,
                'user_id' => null,
                'message' => $aiResponse,
                'is_ai_response' => true,
                'parent_message_id' => $userMessage->id,
            ]);

            \Log::info('GroupChat: AI message saved to database', [
                'ai_message_id' => $aiMessage->id,
                'message_content' => $aiMessage->message,
            ]);

            // Broadcast the AI response directly (immediate)
            $broadcaster = app('Illuminate\Contracts\Broadcasting\Broadcaster');
            $event = new \App\Events\GroupMessageSent($aiMessage);
            $broadcaster->broadcast($event->broadcastOn(), $event->broadcastAs(), $event->broadcastWith());

            $this->group->load(['messages.user', 'files.knowledgeFile', 'members' => function ($query) {
                $query->withPivot('role');
            }]);

            \Log::info('GroupChat: Group refreshed, total messages', [
                'message_count' => $this->group->messages->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('GroupChat: Exception in generateAIResponse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Failed to generate AI response: '.$e->getMessage());
        }
    }

    /**
     * Check if the current user is the group owner
     */
    protected function isGroupOwner(): bool
    {
        return $this->group->owner_id === Auth::id();
    }

    /**
     * Computed property for view access
     */
    public function getIsOwnerProperty(): bool
    {
        return $this->isGroupOwner();
    }

    public function uploadFileToGroup(): void
    {
        // Only group owner can upload files
        if (! $this->isGroupOwner()) {
            session()->flash('error', 'Only the group owner can upload files.');

            return;
        }

        // Ensure user has a business relationship (create one if needed)
        $user = Auth::user();
        if (! $user->business) {
            $business = Business::create([
                'name' => $user->name."'s Business",
            ]);
            $user->business_id = $business->id;
            $user->save();
            $user->refresh(); // Refresh the user to load the new business relationship
        }

        $this->validate([
            'uploadFile' => 'required|file|mimes:pdf,txt,docx|max:10240',
        ]);

        // Check if group already has a file
        $existingFile = $this->group->files()->first();

        if ($existingFile) {
            // Remove the existing file and its knowledge base
            $oldKnowledgeFile = $existingFile->knowledgeFile;

            // Delete the old file from storage
            if ($oldKnowledgeFile && \Storage::exists($oldKnowledgeFile->storage_path)) {
                \Storage::delete($oldKnowledgeFile->storage_path);
            }

            // Delete text chunks associated with the old file
            if ($oldKnowledgeFile) {
                $oldKnowledgeFile->textChunks()->delete();
                $oldKnowledgeFile->delete();
            }

            // Delete the group file relationship
            $existingFile->delete();

            session()->flash('message', 'Previous file replaced with new upload!');
        } else {
            session()->flash('message', 'File uploaded and processing started!');
        }

        $path = $this->uploadFile->store('knowledge_files');

        $knowledgeFile = Auth::user()->business->knowledgeFiles()->create([
            'original_name' => $this->uploadFile->getClientOriginalName(),
            'storage_path' => $path,
            'status' => 'pending',
        ]);

        GroupFile::create([
            'group_id' => $this->group->id,
            'knowledge_file_id' => $knowledgeFile->id,
            'uploaded_by' => Auth::id(),
        ]);

        \App\Jobs\ProcessKnowledgeFile::dispatch($knowledgeFile);

        $this->reset('uploadFile');
        $this->group->load(['messages.user', 'files.knowledgeFile', 'members' => function ($query) {
            $query->withPivot('role');
        }]);
    }

    public function inviteMember(): void
    {
        // Only group owner can invite members
        if (! $this->isGroupOwner()) {
            session()->flash('error', 'Only the group owner can invite members.');

            return;
        }

        $this->validate([
            'inviteEmail' => 'required|email|exists:users,email',
        ]);

        $userToInvite = \App\Models\User::where('email', $this->inviteEmail)->first();

        // Check if user is already a member
        if ($this->group->members->contains($userToInvite->id)) {
            session()->flash('error', 'This user is already a member of the group.');

            return;
        }

        // Add user as member
        \App\Models\GroupMember::create([
            'group_id' => $this->group->id,
            'user_id' => $userToInvite->id,
            'role' => 'member',
        ]);

        $this->reset('inviteEmail');
        $this->group->load(['members' => function ($query) {
            $query->withPivot('role');
        }]);
        session()->flash('message', $userToInvite->name.' has been added to the group!');
    }

    public function removeMember(int $userId): void
    {
        // Only owner can remove members
        $currentUserRole = $this->group->members()
            ->where('user_id', Auth::id())
            ->first()->pivot->role;

        if ($currentUserRole !== 'owner' && $userId !== Auth::id()) {
            session()->flash('error', 'Only the group owner can remove members.');

            return;
        }

        // Can't remove the owner
        $memberToRemove = $this->group->members()
            ->where('user_id', $userId)
            ->first();

        if ($memberToRemove->pivot->role === 'owner') {
            session()->flash('error', 'Cannot remove the group owner.');

            return;
        }

        // Get member name for better feedback
        $memberName = $memberToRemove->name;
        $isCurrentUser = $userId === Auth::id();

        \App\Models\GroupMember::where('group_id', $this->group->id)
            ->where('user_id', $userId)
            ->delete();

        $this->group->load(['members' => function ($query) {
            $query->withPivot('role');
        }]);

        // Different messages for leaving vs being removed
        if ($isCurrentUser) {
            session()->flash('message', 'You have left the group successfully.');
            // Redirect to groups list after leaving
            $this->redirect('/groups');
        } else {
            session()->flash('message', $memberName.' has been removed from the group.');
        }
    }

    public function render()
    {
        return view('livewire.groups.group-chat')->layout('layouts.app');
    }
}
