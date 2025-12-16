<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use App\Telegram\Middleware\TimeZoneMiddleware;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);
        bot()->middleware(RequestMiddleware::class);
        bot()->middleware(TimeZoneMiddleware::class);

        bot()->onCommand('start', function () {
            $this->try(function () {
                $state = new MenuBotState();

                $userDto = UserState::load(bot()->userId());

                if ($userDto) {
                    request()->merge([
                        'path' => $userDto->path
                    ]);
                    bot()->sendMessage('Восстановлено предыдущее состояние');
                } else {
                    $userDto = UserState::make(bot()->userId(), troute('home'), $state);
                    UserState::write($userDto);
                    bot()->sendMessage('Обратите внимание на часовой пояс в настройках');
                }

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
        logger()->debug('handle state');

        $userDto = UserState::load(bot()->userId());

        if (!empty($userDto->state)) {
            logger()->debug('use state form user cache');
            $current = $userDto->state;
        } else {
            logger()->debug('user cache not found');
            bot()->sendMessage('Вы долго не заходили ко мне. Ваше состояние потеряно. Начните сначала');
            Keyboard::remove();
            $current = new MenuBotState();
            logger()->debug('use default state');

            UserState::write(UserState::make(bot()->userId(), request('path'), $current));
        }

        logger()->debug('current state is: ' . get_class($current));

        logger()->debug('user state after handle: ' . json_encode($userDto));

        $next = $current->handle();
        logger()->debug('state handled');

        /** в handle состояние user могло поменятся */
        $userDto = UserState::load(bot()->userId());
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
            if (app()->hasDebugModeEnabled()) {
                bot()->sendMessage("Error! " . $e->getMessage());
            } else {
                bot()->sendMessage("Произошла ошибка");
            }
        });
    }
}
