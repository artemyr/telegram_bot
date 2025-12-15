<?php

namespace Domain\TelegramBot;

use Illuminate\Support\Facades\Cache;

class UserStateStore
{
    public static function get(int $userId): ?BotState
    {
        return Cache::get("tg_state:$userId");
    }

    public static function set(int $userId, BotState $state): void
    {
        Cache::put("tg_state:$userId", $state, now()->addHour());
    }
}
