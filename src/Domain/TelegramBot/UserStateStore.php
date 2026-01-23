<?php

namespace Domain\TelegramBot;

use Domain\TelegramBot\Dto\UserStateDto;
use Illuminate\Support\Facades\Cache;

class UserStateStore
{
    public static function get(string $botName, int $userId): ?UserStateDto
    {
        return Cache::get("tg_state:$botName:$userId");
    }

    public static function set(string $botName, int $userId, UserStateDto $user): void
    {
        Cache::put("tg_state:$botName:$userId", $user, config('telegram_bot.auth.user_state_lock_period'));
    }

    public static function forget(string $botName, int $userId): void
    {
        Cache::forget("tg_state:$botName:$userId");
    }
}
