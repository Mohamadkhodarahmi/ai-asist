<?php

namespace App\Policies;

use App\Models\TelegramBot;
use App\Models\User;

class TelegramBotPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Users can view list of bots they have access to
    }

    public function view(User $user, TelegramBot $telegramBot): bool
    {
        return $user->business_id === $telegramBot->business_id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a bot for their business
    }

    public function update(User $user, TelegramBot $telegramBot): bool
    {
        return $user->business_id === $telegramBot->business_id;
    }

    public function delete(User $user, TelegramBot $telegramBot): bool
    {
        return $user->business_id === $telegramBot->business_id;
    }
}