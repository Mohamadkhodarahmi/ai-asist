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
