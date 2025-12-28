<?php

use App\Menu\MenuContract;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\UserStateDto;
use SergiX44\Nutgram\Nutgram;
use Support\Contracts\HumanDateParserContract;

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
            if ($fail !== null) {
                $fail($e);
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
        return tuserstate()->get();
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

if (!function_exists('message')) {
    function message(?string $text = null): MessageContract
    {
        if (!empty($text)) {
            app(MessageContract::class)->text($text)->send();
        }

        return app(MessageContract::class);
    }
}

if (!function_exists('keyboard')) {
    function keyboard(): KeyboardContract
    {
        return app(KeyboardContract::class);
    }
}

if (!function_exists('tuserstate')) {
    function tuserstate(): UserStateContract
    {
        return app(UserStateContract::class);
    }
}

if (!function_exists('humandateparser')) {
    function humandateparser(?string $date = null): HumanDateParserContract
    {
        if (!empty($date)) {
            return app(HumanDateParserContract::class)->fromString($date);
        }

        return app(HumanDateParserContract::class);
    }
}
