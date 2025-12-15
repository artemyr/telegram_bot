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
                'path' => $bot->callbackQuery()->data,
                'can_send_answer_silent' => !empty($path),
            ]);
        } else {
            request()->merge([
                'path' => troute('home'),
                'can_send_answer_silent' => !empty($path),
            ]);
        }

        logger()->debug('Request to: ' . request('path'));

        $next($bot);
    }
}
