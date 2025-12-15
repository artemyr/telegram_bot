<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class RequestMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (!empty($bot->callbackQuery()->data)) {
            request()->merge([
                'path' => $bot->callbackQuery()->data
            ]);
        } else {
            request()->merge([
                'path' => '/'
            ]);
        }

        $next($bot);
    }
}
