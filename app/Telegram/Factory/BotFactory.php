<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\UserStateStore;
use SergiX44\Nutgram\Nutgram;

class BotFactory
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(RequestMiddleware::class);

        $fail = function ($e) use ($bot) {
            if (app()->hasDebugModeEnabled()) {
                $bot->sendMessage("Error! " . $e->getMessage());
            }
        };

        $bot->onCommand('start', function (Nutgram $bot) use ($fail) {
            try_to(function () use ($bot) {
                $state = new MenuBotState();
                UserStateStore::set($bot->userId(), $state);
                $state->render($bot);
            }, $fail);
        });

        $bot->onCallbackQuery(function (Nutgram $bot) use ($fail) {
            try_to(function () use ($bot) {
                $current = UserStateStore::get($bot->userId()) ?? new MenuBotState();
                $next = $current->handle($bot);

                if ($next) {
                    $next->silent();
                    UserStateStore::set($bot->userId(), $next);
                    $next->render($bot);
                }
            }, $fail);
        });

        try_to(function () use ($bot) {
            if (app()->isLocal()) {
                $bot->registerMyCommands();
            }
        }, $fail);
    }
}
