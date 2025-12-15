<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class RequestMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $path = $bot->callbackQuery()?->data;

        if (!empty($path)) {
            request()->merge([
                'path' => $bot->callbackQuery()->data
            ]);
        } else {
            request()->merge([
                'path' => troute('home')
            ]);
        }

        logger()->debug('Request to: ' . request('path'));

        $next($bot);
    }
}
