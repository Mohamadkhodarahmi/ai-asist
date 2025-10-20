<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    // Check if user is a member of this group
    return \App\Models\Group::find($groupId)
        ?->members()
        ->where('user_id', $user->id)
        ->exists();
});

Broadcast::channel('voice-channel.{voiceChannelId}', function ($user, $voiceChannelId) {
    // Check if user is a member of the group that owns this voice channel
    $voiceChannel = \App\Models\VoiceChannel::find($voiceChannelId);
    if (! $voiceChannel) {
        return false;
    }

    return $voiceChannel->group
        ?->members()
        ->where('user_id', $user->id)
        ->exists();
});
