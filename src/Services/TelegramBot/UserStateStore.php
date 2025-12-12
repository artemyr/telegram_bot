<?php

namespace Services\TelegramBot;

use Illuminate\Support\Facades\Cache;
use Services\TelegramBot\Menu\MenuState;

class UserStateStore
{
    public static function get(int $userId): ?MenuState
    {
        return Cache::get("tg_state:$userId");
    }

    public static function set(int $userId, MenuState $state): void
    {
        Cache::put("tg_state:$userId", $state, now()->addSeconds(10));
    }
}
