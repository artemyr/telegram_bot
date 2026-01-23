<?php

use App\Menu\MenuContract;
use App\Telegram\Contracts\BotInstanceContract;
use App\Telegram\Contracts\UserInstanceContract;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Models\TelegramUser;
use Nutgram\Laravel\RunningMode\LaravelWebhook;
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

if (!function_exists('travel_bot')) {
    function travel_bot(): Nutgram
    {
        $bot = new Nutgram(config('nutgram.bots.travel'));
        $bot->setRunningMode(LaravelWebhook::class);
        return app()->instance(BotInstanceContract::class, $bot);
    }
}

if (!function_exists('schedule_bot')) {
    function schedule_bot(): Nutgram
    {
        $bot = new Nutgram(config('nutgram.bots.schedule'));
        $bot->setRunningMode(LaravelWebhook::class);
        return app()->instance(BotInstanceContract::class, $bot);
    }
}

if (!function_exists('bot')) {
    function bot(): Nutgram
    {
        return app(BotInstanceContract::class);
    }
}

if (!function_exists('schedule_user')) {
    function schedule_user(): UserStateContract
    {
        /** @var UserStateContract $userState */
        $userState = app(UserStateContract::class);
        $userState->setBotName('schedule');
        return app()->instance(UserInstanceContract::class, $userState);
    }
}

if (!function_exists('travel_user')) {
    function travel_user(): UserStateContract
    {
        /** @var UserStateContract $userState */
        $userState = app(UserStateContract::class);
        $userState->setBotName('travel');
        return app()->instance(UserInstanceContract::class, $userState);
    }
}

if (!function_exists('tuser')) {
    function tuser(): UserStateContract
    {
        return app(UserInstanceContract::class);
    }
}

if (!function_exists('tusertimezone')) {
    function tusertimezone(): string
    {
        $tuser = TelegramUser::query()
            ->where('telegram_id', tuser()->get()->userId)
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

if (!function_exists('humandateparser')) {
    function humandateparser(?string $date = null, ?string $tz = null): HumanDateParserContract
    {
        if (!empty($date)) {
            return app(HumanDateParserContract::class)->fromString($date, $tz);
        }

        return app(HumanDateParserContract::class);
    }
}
