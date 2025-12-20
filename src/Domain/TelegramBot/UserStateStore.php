<?php

namespace Domain\TelegramBot;

use Domain\TelegramBot\Dto\UserStateDto;
use Illuminate\Support\Facades\Cache;

class UserStateStore
{
    public static function get(int $userId): ?UserStateDto
    {
        return Cache::get("tg_state:$userId");
    }

    public static function set(int $userId, UserStateDto $user): void
    {
        Cache::put("tg_state:$userId", $user, config('auth.telegram.user_state_lock_period'));
    }

    public static function forget(int $userId): void
    {
        Cache::forget("tg_state:$userId");
    }
}
