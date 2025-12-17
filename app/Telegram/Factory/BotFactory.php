<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\TimeZoneMiddleware;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);
        bot()->middleware(TimeZoneMiddleware::class);

        bot()->onCommand('start', function () {
            $this->try(function () {
                $userDto = UserState::get(bot()->userId());

                if (config('telegram_bot.debug', false)) {
                    bot()->sendMessage("Состояние пользователя: \n```\n$userDto\n```");
                }

                if ($userDto) {
                    bot()->sendMessage('Восстановлено предыдущее состояние');
                } else {
                    UserState::load(bot()->userId());
                    bot()->sendMessage('Обратите внимание на часовой пояс в настройках');
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

        $this->try(function () {
            if (app()->isLocal()) {
                bot()->registerMyCommands();
            }
        });
    }

    protected function handleState(): void
    {
        logger()->debug('handle state start');

        $userDto = UserState::get(bot()->userId());

        if (config('telegram_bot.debug', false)) {
            bot()->sendMessage("Состояние пользователя: \n```\n$userDto\n```");
        }

        if ($userDto && !empty($userDto->state)) {
            logger()->debug('use state form user cache');
            $current = $userDto->state;
        } else {
            logger()->debug('user cache not found');
            bot()->sendMessage('Вы долго не заходили ко мне. Ваше состояние потеряно. Начните сначала');
            Keyboard::remove();
            $current = new MenuBotState();
            logger()->debug('use default state');

            UserState::load(bot()->userId());
        }

        logger()->debug('current state is: ' . get_class($current));

        logger()->debug('user state after handle: ' . json_encode($userDto));

        $next = $current->handle();
        logger()->debug('state handled');

        /** в handle состояние user могло поменятся */
        $userDto = tuser();
        logger()->debug('user state before handle: ' . json_encode($userDto));

        if ($next) {
            logger()->debug('next state is: ' . get_class($next));

            $next->silent();

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
                bot()->sendMessage($e->getMessage());
                return;
            }

            if (app()->hasDebugModeEnabled()) {
                bot()->sendMessage("Error! " . $e->getMessage());
            } else {
                bot()->sendMessage("Произошла ошибка");
            }

            report($e);
        });
    }
}
