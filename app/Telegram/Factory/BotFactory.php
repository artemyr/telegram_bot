<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\UserStateStore;
use SergiX44\Nutgram\Nutgram;

class BotFactory
{
    protected Nutgram $bot;

    public function __invoke(Nutgram $bot): void
    {
        $this->bot = $bot;

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(RequestMiddleware::class);

        $bot->onCommand('start', function (Nutgram $bot) {
            $this->try(function () use ($bot) {
                $state = new MenuBotState();
                UserStateStore::set($bot->userId(), $state);
                $state->render($bot);
            });
        });

        $bot->onCallbackQuery(function (Nutgram $bot) {
            $this->try(function () use ($bot) {
                $this->handleState($bot);
            });
        });

        $bot->onMessage(function (Nutgram $bot) {
            $this->try(function () use ($bot) {
                $this->handleState($bot);
            });
        });

        $this->try(function () {
            if (app()->isLocal()) {
                $this->bot->registerMyCommands();
            }
        });
    }

    protected function handleState($bot): void
    {
        $current = UserStateStore::get($bot->userId()) ?? new MenuBotState();
        $next = $current->handle($bot);

        if ($next) {
            $next->silent();
            UserStateStore::set($bot->userId(), $next);
            $next->render($bot);
        }
    }

    protected function try(callable $call): void
    {
        try_to($call, function ($e) {
            if (app()->hasDebugModeEnabled()) {
                $this->bot->sendMessage("Error! " . $e->getMessage());
            } else {
                $this->bot->sendMessage("Произошла ошибка");
            }
        });
    }
}
