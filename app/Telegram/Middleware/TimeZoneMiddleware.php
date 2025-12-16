<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class TimeZoneMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $userDto = tuser();
        if ($userDto && !empty($userDto->timezone)) {
            config(['app.timezone' => $userDto->timezone]);
        }

        $next($bot);
    }
}
