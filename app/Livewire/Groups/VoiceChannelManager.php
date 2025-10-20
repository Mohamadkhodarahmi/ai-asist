<?php

namespace App\Livewire\Groups;

use App\Events\VoiceChannelJoined;
use App\Events\VoiceChannelLeft;
use App\Events\VoiceChannelSpeaking;
use App\Models\Group;
use App\Models\VoiceChannel;
use App\Models\VoiceSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VoiceChannelManager extends Component
{
    public Group $group;

    public ?VoiceChannel $currentVoiceChannel = null;

    public ?VoiceSession $currentSession = null;

    public bool $isMuted = false;

    public bool $isDeafened = false;

    public bool $isSpeaking = false;

    public string $newChannelName = '';

    public string $newChannelDescription = '';

    protected $listeners = [
        'voiceChannelJoined' => 'handleVoiceChannelJoined',
        'voiceChannelLeft' => 'handleVoiceChannelLeft',
        'voiceChannelSpeaking' => 'handleVoiceChannelSpeaking',
    ];

    public function mount(Group $group): void
    {
        $this->group = $group;

        // Load user's current voice session if any (only from this group's voice channels)
        $this->currentSession = VoiceSession::whereHas('voiceChannel', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->with('voiceChannel')
            ->first();

        if ($this->currentSession) {
            $this->currentVoiceChannel = $this->currentSession->voiceChannel;
            $this->isMuted = $this->currentSession->is_muted;
            $this->isDeafened = $this->currentSession->is_deafened;
            $this->isSpeaking = $this->currentSession->is_speaking;

            // Dispatch event to set the current voice channel ID in JavaScript
            $this->dispatch('voice-channel-joined', ['voiceChannelId' => $this->currentVoiceChannel->id]);
            $this->dispatch('voiceChannelJoined', ['voiceChannelId' => $this->currentVoiceChannel->id]);
        }
    }

    public function createVoiceChannel(): void
    {
        $this->validate([
            'newChannelName' => 'required|string|max:255',
            'newChannelDescription' => 'nullable|string|max:1000',
        ]);

        $voiceChannel = VoiceChannel::create([
            'group_id' => $this->group->id,
            'name' => $this->newChannelName,
            'description' => $this->newChannelDescription,
            'is_active' => true,
            'max_participants' => 10,
        ]);

        $this->reset(['newChannelName', 'newChannelDescription']);
        session()->flash('message', 'Voice channel created successfully!');
    }

    public function joinVoiceChannel($voiceChannelId): void
    {
        $voiceChannel = VoiceChannel::find($voiceChannelId);

        if (! $voiceChannel) {
            session()->flash('error', 'Voice channel not found!');

            return;
        }

        \Log::info('JoinVoiceChannel method called', [
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => Auth::id(),
            'voice_channel_name' => $voiceChannel->name,
        ]);

        // Check if user is already in this channel
        $existingSession = VoiceSession::where('voice_channel_id', $voiceChannel->id)
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->first();

        if ($existingSession) {
            // User is already in this channel, just switch to it
            if ($this->currentVoiceChannel && $this->currentVoiceChannel->id !== $voiceChannel->id) {
                $this->leaveVoiceChannel();
            }

            $this->currentVoiceChannel = $voiceChannel;
            $this->currentSession = $existingSession;
            $this->isMuted = $existingSession->is_muted;
            $this->isDeafened = $existingSession->is_deafened;
            $this->isSpeaking = $existingSession->is_speaking;

            session()->flash('message', 'Already in this voice channel!');

            return;
        }

        // Leave current channel if in one
        if ($this->currentVoiceChannel) {
            $this->leaveVoiceChannel();
        }

        // Check if channel is full
        if ($voiceChannel->isFull()) {
            session()->flash('error', 'Voice channel is full!');

            return;
        }

        // Create voice session with proper error handling
        try {

            $session = VoiceSession::create([
                'voice_channel_id' => $voiceChannel->id,
                'user_id' => Auth::id(),
                'joined_at' => now(),
            ]);

            $this->currentVoiceChannel = $voiceChannel;
            $this->currentSession = $session;
            $this->isMuted = false;
            $this->isDeafened = false;
            $this->isSpeaking = false;

            \Log::info('Voice channel joined successfully', [
                'current_voice_channel_id' => $this->currentVoiceChannel->id,
                'session_id' => $session->id,
                'component_state_after_set' => [
                    'currentVoiceChannel' => $this->currentVoiceChannel ? $this->currentVoiceChannel->id : 'null',
                    'currentSession' => $this->currentSession ? $this->currentSession->id : 'null',
                ],
            ]);

            // Broadcast event
            broadcast(new VoiceChannelJoined($voiceChannel, Auth::user()));

            // Dispatch event to set current voice channel ID in JavaScript
            $this->dispatch('voice-channel-joined', ['voiceChannelId' => $voiceChannel->id]);
            $this->dispatch('voiceChannelJoined', ['voiceChannelId' => $voiceChannel->id]);

            // Auto-start voice stream when joining
            $this->dispatch('start-voice-stream', ['voiceChannelId' => $voiceChannel->id]);

            session()->flash('message', 'Joined voice channel!');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Handle the case where user somehow already exists
            $existingSession = VoiceSession::where('voice_channel_id', $voiceChannel->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingSession && is_null($existingSession->left_at)) {
                // User is already in the channel
                $this->currentVoiceChannel = $voiceChannel;
                $this->currentSession = $existingSession;
                $this->isMuted = $existingSession->is_muted;
                $this->isDeafened = $existingSession->is_deafened;
                $this->isSpeaking = $existingSession->is_speaking;

                session()->flash('message', 'Already in this voice channel!');
            } else {
                session()->flash('error', 'Unable to join voice channel. Please try again.');
            }
        }
    }

    public function leaveVoiceChannel(): void
    {
        if (! $this->currentSession) {
            return;
        }

        $voiceChannel = $this->currentVoiceChannel;
        $user = Auth::user();

        // Leave the session
        $this->currentSession->leave();

        // Reset state
        $this->currentVoiceChannel = null;
        $this->currentSession = null;
        $this->isMuted = false;
        $this->isDeafened = false;
        $this->isSpeaking = false;

        // Broadcast event
        broadcast(new VoiceChannelLeft($voiceChannel, $user));

        // Dispatch event to update JavaScript state
        $this->dispatch('voice-channel-left', ['voiceChannelId' => $voiceChannel->id]);

        session()->flash('message', 'Left voice channel!');
    }

    public function toggleMute(): void
    {
        if (! $this->currentSession) {
            return;
        }

        $this->currentSession->toggleMute();
        $this->isMuted = $this->currentSession->is_muted;
    }

    public function toggleDeafen(): void
    {
        if (! $this->currentSession) {
            return;
        }

        $this->currentSession->toggleDeafen();
        $this->isDeafened = $this->currentSession->is_deafened;
    }

    public function startSpeaking(): void
    {
        if (! $this->currentSession || $this->isSpeaking) {
            return;
        }

        $this->currentSession->setSpeaking(true);
        $this->isSpeaking = true;

        // Broadcast speaking state
        broadcast(new VoiceChannelSpeaking($this->currentVoiceChannel, Auth::user(), true));
    }

    public function stopSpeaking(): void
    {
        if (! $this->currentSession || ! $this->isSpeaking) {
            return;
        }

        $this->currentSession->setSpeaking(false);
        $this->isSpeaking = false;

        // Broadcast speaking state
        broadcast(new VoiceChannelSpeaking($this->currentVoiceChannel, Auth::user(), false));
    }

    public function deleteVoiceChannel(VoiceChannel $voiceChannel): void
    {
        // Only group owner can delete voice channels
        if ($this->group->owner_id !== Auth::id()) {
            session()->flash('error', 'Only the group owner can delete voice channels!');

            return;
        }

        // Kick all users from the channel
        $voiceChannel->sessions()->whereNull('left_at')->update(['left_at' => now()]);

        $voiceChannel->delete();
        session()->flash('message', 'Voice channel deleted!');
    }

    public function handleVoiceChannelJoined($event): void
    {
        // Refresh the component to show updated participant list
        $this->dispatch('$refresh');
    }

    public function handleVoiceChannelLeft($event): void
    {
        // Refresh the component to show updated participant list
        $this->dispatch('$refresh');
    }

    public function handleVoiceChannelSpeaking($event): void
    {
        // Update speaking indicators in the UI
        $this->dispatch('voice-speaking-updated', $event);
    }

    public function updateSpeakingStatus(bool $isSpeaking): void
    {
        if ($this->currentSession) {
            $this->currentSession->setSpeaking($isSpeaking);
            $this->isSpeaking = $isSpeaking;

            \Log::info('Speaking status updated via Livewire', [
                'user_id' => Auth::id(),
                'voice_channel_id' => $this->currentVoiceChannel->id,
                'is_speaking' => $isSpeaking,
                'session_id' => $this->currentSession->id,
            ]);

            // Broadcast the speaking event
            broadcast(new VoiceChannelSpeaking($this->currentVoiceChannel, Auth::user(), $isSpeaking));
        }
    }

    public function getCurrentVoiceChannelId(): ?int
    {
        return $this->currentVoiceChannel?->id;
    }

    public function render()
    {
        $this->group->load(['voiceChannels.activeSessions.user']);

        return view('livewire.groups.voice-channel-manager', [
            'voiceChannels' => $this->group->voiceChannels,
            'isOwner' => $this->group->owner_id === Auth::id(),
        ]);
    }
}
