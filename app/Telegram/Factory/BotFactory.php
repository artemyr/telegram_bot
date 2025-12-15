<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\UserStateStore;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);
        bot()->middleware(RequestMiddleware::class);

        bot()->onCommand('start', function () {
            $this->try(function () {
                $state = new MenuBotState();
                UserStateStore::set(bot()->userId(), $state);
                $state->render();
            });
        });

        bot()->onCallbackQuery(function () {
            $this->try(function () {
                $this->handleState();
            });
        });

        bot()->onMessage(function () {
            $this->try(function () {
                $this->handleState();
            });
        });

        $this->try(function () {
            if (app()->isLocal()) {
                bot()->registerMyCommands();
            }
        });
    }

    protected function handleState(): void
    {
        $current = UserStateStore::get(bot()->userId()) ?? new MenuBotState();
        $next = $current->handle();

        if ($next) {
            $next->silent();
            UserStateStore::set(bot()->userId(), $next);
            $next->render();
        }
    }

    protected function try(callable $call): void
    {
        try_to($call, function ($e) {
            if (app()->hasDebugModeEnabled()) {
                bot()->sendMessage("Error! " . $e->getMessage());
            } else {
                bot()->sendMessage("Произошла ошибка");
            }
        });
    }
}
