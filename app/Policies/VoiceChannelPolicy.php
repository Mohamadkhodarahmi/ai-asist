<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use App\Models\VoiceChannel;

class VoiceChannelPolicy
{
    public function view(User $user, Group $group): bool
    {
        return $group->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user, Group $group): bool
    {
        return $group->owner_id === $user->id || $group->userHasPermission($user, 'manage_group');
    }

    public function update(User $user, Group $group): bool
    {
        return $group->owner_id === $user->id || $group->userHasPermission($user, 'manage_group');
    }

    public function delete(User $user, Group $group): bool
    {
        return $group->owner_id === $user->id || $group->userHasPermission($user, 'manage_group');
    }

    public function join(User $user, VoiceChannel $voiceChannel): bool
    {
        return $voiceChannel->group->members()->where('user_id', $user->id)->exists();
    }

    public function leave(User $user, VoiceChannel $voiceChannel): bool
    {
        return $voiceChannel->group->members()->where('user_id', $user->id)->exists();
    }

    public function speak(User $user, VoiceChannel $voiceChannel): bool
    {
        // User can speak if they are a member of the group that owns this voice channel
        return $voiceChannel->group->members()->where('user_id', $user->id)->exists();
    }
}
