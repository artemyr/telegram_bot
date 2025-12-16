<?php

namespace App\Telegram\Middleware;

use Domain\TelegramBot\Facades\UserState;
use SergiX44\Nutgram\Nutgram;

class TimeZoneMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $userDto = UserState::load($bot->userId());
        if ($userDto && !empty($userDto->timezone)) {
            config(['app.timezone' => $userDto->timezone]);
        }

        $next($bot);
    }
}
