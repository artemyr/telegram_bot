<?php

namespace Domain\TelegramBot;

use Domain\TelegramBot\Dto\UserDto;
use Illuminate\Support\Facades\Cache;

class UserStateStore
{
    public static function get(int $userId): ?UserDto
    {
        return Cache::get("tg_state:$userId");
    }

    public static function set(int $userId, UserDto $user): void
    {
        Cache::put("tg_state:$userId", $user, config('auth.telegram.user_state_lock_period'));
    }
}
