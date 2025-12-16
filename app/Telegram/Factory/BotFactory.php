<?php

namespace App\Telegram\Factory;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use App\Telegram\Middleware\TimeZoneMiddleware;
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
        $userDto = UserState::load(bot()->userId());

        $current = $userDto->state;
        $current = ($current) ?: new MenuBotState();

        $next = $current->handle();

        if ($next) {
            $next->silent();

            /** в handle состояние user могло поменятся */
            $userDto = UserState::load(bot()->userId());

            $user = UserState::make(
                bot()->userId(),
                request('path'),
                $next,
                $userDto?->timezone ?? '',
                $userDto?->keyboard ?? false,
                $userDto?->actions ?? []
            );

            UserState::write($user);
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
