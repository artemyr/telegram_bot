<?php

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Domain\TelegramBot\Contracts\BotContract;
use Domain\TelegramBot\Contracts\BotInstanceContract;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\NotificationInstanceContract;
use Domain\TelegramBot\Contracts\UserInstanceContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Factory\AbstractBotFactory;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\Models\TelegramUser;
use Domain\TelegramBot\Services\BotManager;
use Illuminate\Support\Carbon;
use SergiX44\Nutgram\Nutgram;
use Support\Contracts\HumanDateParserContract;

if (!function_exists('menu')) {
    function menu(): ?MenuContract
    {
        $tuser = tuser()->get();
        $state = $tuser?->state;

        if ($state instanceof MenuBotState) {
            $path = $state->getPath();
            MenuItem::setCurrentPath($path);
        } else {
            MenuItem::setCurrentPath(troute('home'));
        }

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

if (!function_exists('nutgram')) {
    function nutgram(): Nutgram
    {
        return app(BotInstanceContract::class);
    }
}

if (!function_exists('bot')) {
    function bot(): BotContract
    {
        return app(BotContract::class);
    }
}

if (!function_exists('init_bot')) {
    function init_bot($factory, bool $polling = false): Nutgram
    {
        if (empty($factory) || !$factory instanceof AbstractBotFactory) {
            throw new RuntimeException('Factory must implement AbstractBotFactory');
        }

        $f = new $factory;

        $botManager = app()->instance(BotContract::class, new BotManager($f, $polling));
        return $botManager->current();
    }
}

if (!function_exists('notify')) {
    function notify(string $message, Carbon $date): NotificationInstanceContract
    {
        /** @var NotificationInstanceContract $notify */
        $notify = app(NotificationInstanceContract::class);
        $notify->send($message, $date);
        return $notify;
    }
}

if (!function_exists('tuser')) {
    function tuser(): UserStateContract
    {
        return app(UserInstanceContract::class);
    }
}

if (!function_exists('tusertimezone')) {
    function tusertimezone(?int $tuserId = null): string
    {
        if (empty($tuserId)) {
            $tuserId = \nutgram()->userId();
        }

        $tuser = TelegramUser::query()
            ->where('telegram_id', $tuserId)
            ->first();

        if ($tuser) {
            return $tuser->timezone;
        }

        return config('app.timezone');
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
