<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

class PlanLimiter
{
    public function canSendMessage(User $user, ?CarbonInterface $now = null): bool
    {
        $now = $now ?? now();

        $limit = (int) ($user->plan?->daily_message_limit ?? 0);

        if ($limit <= 0) {
            // 0 or null means unlimited for paid plans; for free plan we will set explicit value.
            return true;
        }

        $key = $this->key($user, $now);
        $count = (int) Cache::get($key, 0);

        return $count < $limit;
    }

    public function hit(User $user, ?CarbonInterface $now = null): void
    {
        $now = $now ?? now();
        $key = $this->key($user, $now);

        $expiresAt = $now->copy()->endOfDay();
        Cache::add($key, 0, $expiresAt);
        Cache::increment($key);
    }

    private function key(User $user, CarbonInterface $now): string
    {
        return sprintf('plan:daily_messages:%d:%s', $user->id, $now->toDateString());
    }
}


