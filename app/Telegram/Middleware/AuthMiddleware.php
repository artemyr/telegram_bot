<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class AuthMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $user = $bot->user();

        if (!in_array($user?->id, config('auth.telegram.user_ids', []))) {
            logger()->alert(
                sprintf(
                    "Some user try to access bot:\n"
                    . "user_id: '%s', \n"
                    . "username: '%s', \n"
                    . "name: %s",
                    $user?->id,
                    $user?->username,
                    $user?->first_name .' '. $user?->last_name,
                )
            );

            return;
        }

        $next($bot);
    }
}
