<?php

namespace App\Http\Controllers;

use App\Events\VoiceChannelJoined;
use App\Events\VoiceChannelLeft;
use App\Events\VoiceChannelSpeaking;
use App\Models\Group;
use App\Models\VoiceChannel;
use App\Models\VoiceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceChannelController extends Controller
{
    public function store(Request $request, Group $group): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $group);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        VoiceChannel::create([
            'group_id' => $group->id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
            'max_participants' => 10,
        ]);

        return redirect()->route('groups.chat', $group)
            ->with('message', 'Voice channel created successfully!');
    }

    public function join(Group $group, VoiceChannel $voiceChannel): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('view', $group);

        // Check if channel is full
        if ($voiceChannel->isFull()) {
            return redirect()->route('groups.chat', $group)
                ->with('error', 'Voice channel is full!');
        }

        // Check if user is already in the channel
        if ($voiceChannel->hasUser(Auth::id())) {
            return redirect()->route('groups.chat', $group)
                ->with('error', 'You are already in this voice channel!');
        }

        // Create voice session
        VoiceSession::create([
            'voice_channel_id' => $voiceChannel->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        // Broadcast event
        broadcast(new VoiceChannelJoined($voiceChannel, Auth::user()));

        return redirect()->route('groups.chat', $group)
            ->with('message', 'Joined voice channel!');
    }

    public function leave(Group $group, VoiceChannel $voiceChannel): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('view', $group);

        $session = VoiceSession::where('voice_channel_id', $voiceChannel->id)
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->first();

        if ($session) {
            $session->leave();
            broadcast(new VoiceChannelLeft($voiceChannel, Auth::user()));
        }

        return redirect()->route('groups.chat', $group)
            ->with('message', 'Left voice channel!');
    }

    public function destroy(Group $group, VoiceChannel $voiceChannel): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $group);

        // Kick all users from the channel
        $voiceChannel->sessions()->whereNull('left_at')->update(['left_at' => now()]);

        $voiceChannel->delete();

        return redirect()->route('groups.chat', $group)
            ->with('message', 'Voice channel deleted!');
    }

    public function updateSpeaking(Request $request, Group $group, VoiceChannel $voiceChannel): \Illuminate\Http\JsonResponse
    {
        $this->authorize('speak', $voiceChannel);

        $request->validate([
            'is_speaking' => 'required|boolean',
        ]);

        $session = VoiceSession::where('voice_channel_id', $voiceChannel->id)
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->first();

        if ($session) {
            $session->setSpeaking($request->is_speaking);
            broadcast(new VoiceChannelSpeaking($voiceChannel, Auth::user(), $request->is_speaking));
        }

        return response()->json(['success' => true]);
    }

    public function handleSignaling(Request $request, Group $group, VoiceChannel $voiceChannel): \Illuminate\Http\JsonResponse
    {
        $this->authorize('speak', $voiceChannel);

        $request->validate([
            'type' => 'required|string|in:offer,answer,ice-candidate,test-beep',
            'targetUserId' => 'required|string',
            'offer' => 'nullable|array',
            'answer' => 'nullable|array',
            'candidate' => 'nullable|array',
            'beepData' => 'nullable|array',
        ]);

        $user = Auth::user();

        // Create signaling event
        $signalingData = [
            'from_user_id' => $user->id,
            'from_user_name' => $user->name,
            'type' => $request->type,
            'target_user_id' => $request->targetUserId,
        ];

        // Add type-specific data
        switch ($request->type) {
            case 'offer':
                $signalingData['offer'] = $request->offer;
                break;
            case 'answer':
                $signalingData['answer'] = $request->answer;
                break;
            case 'ice-candidate':
                $signalingData['candidate'] = $request->candidate;
                break;
            case 'test-beep':
                $signalingData['beepData'] = $request->beepData;
                // For test-beep, targetUserId can be 'all' to send to all participants
                $signalingData['target_user_id'] = $request->targetUserId;
                break;
        }

        // Broadcast signaling message to target user
        broadcast(new \App\Events\VoiceChannelSignaling($voiceChannel, $signalingData));

        return response()->json(['success' => true]);
    }
}
