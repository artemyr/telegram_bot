<?php

namespace App\Telegram\Middleware;

use Domain\TelegramBot\Facades\UserState;
use SergiX44\Nutgram\Nutgram;

class RequestMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $path = $bot->callbackQuery()?->data;

        if (!empty($path)) {

            UserState::changePath($bot->userId(), $path);

            request()->merge([
                'path' => $path,
            ]);
        } else {
            $userDto = tuser();

            request()->merge([
                'path' => $userDto?->path ?? troute('home'),
            ]);
        }

        $next($bot);
    }
}
