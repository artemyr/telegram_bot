<?php

use App\Menu\MenuContract;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\Facades\UserState;
use SergiX44\Nutgram\Nutgram;

if (!function_exists('menu')) {
    function menu(): MenuContract
    {
        return app(MenuContract::class);
    }
}

if (!function_exists('troute')) {
    function troute(string $name, array $parameters = []): string
    {
        $route = route($name, $parameters);

        $route = str_replace(env('APP_URL'), '', $route);
        if (empty($route)) {
            $route = '/';
        }

        return $route;
    }
}

if (!function_exists('try')) {
    function try_to(callable $callback, callable $fail = null)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            report($e);

            if ($fail !== null) {
                try {
                    $fail($e);
                } catch (Throwable) {
                }
            }
        }

        return null;
    }
}

if (!function_exists('bot')) {
    function bot(): Nutgram
    {
        return app(Nutgram::class);
    }
}

if (!function_exists('tuser')) {
    function tuser(): ?UserStateDto
    {
        return UserState::load(bot()->userId());
    }
}
