<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);

        bot()->onCommand('start', function () {
            $this->try(function () {
                $userDto = tuser();

                if (config('telegram_bot.debug', false)) {
                    send("Состояние пользователя: \n```\n$userDto\n```");
                }

                $state = $userDto->state ?? new MenuBotState();
                $state->render();
            });
        });

        bot()->onMessage(function () {
            $this->try(function () {
                $this->handleState();
            });
        });
    }

    protected function handleState(): void
    {
        logger()->debug('handle state start');

        $userDto = tuser();

        if (config('telegram_bot.debug', false)) {
            send("Состояние пользователя: \n```\n$userDto\n```");
        }

        $current = $userDto->state;
        logger()->debug('current state is: ' . get_class($current));

        logger()->debug('user state after handle: ' . json_encode($userDto));

        $next = $current->handle();
        logger()->debug('state handled');

        /** в handle состояние user могло поменятся */
        $userDto = tuser();
        logger()->debug('user state before handle: ' . json_encode($userDto));

        if ($next) {
            logger()->debug('next state is: ' . get_class($next));

            UserState::changeState(bot()->userId(), $next);

            logger()->debug('next state written to user');

            $next->render();

            logger()->debug('next state rendered');
        }
    }

    protected function try(callable $call): void
    {
        try_to($call, function ($e) {

            if ($e instanceof PrintableException) {
                send($e->getMessage());
                return;
            }

            if (app()->hasDebugModeEnabled()) {
                send($e->getMessage("Error! " . $e->getMessage()));
            } else {
                send($e->getMessage("Произошла ошибка"));
            }

            report($e);
        });
    }
}
