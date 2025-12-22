<?php

use App\Menu\MenuContract;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\Facades\Message;
use Domain\TelegramBot\Facades\UserState;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

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

        $route = str_replace(config('app.url'), '', $route);
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
        return UserState::get(bot()->userId());
    }
}

if (!function_exists('tusertimezone')) {
    function tusertimezone(): string
    {
        $tuser = \Domain\TelegramBot\Models\TelegramUser::query()
            ->where('telegram_id', tuser()->userId)
            ->first();

        if ($tuser) {
            return $tuser->timezone;
        }

        return app('app.timezone');
    }
}

if (!function_exists('send')) {
    function send(string|array $message, ?ReplyKeyboardMarkup $keyboard = null, ?int $userId = null): void
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        if (empty($userId)) {
            $userId = bot()->userId();
        }

        Message::send($userId, $message, $keyboard);
    }
}
