<?php

use App\Menu\MenuContract;
use App\Telegram\Contracts\BotContract;
use App\Telegram\Contracts\BotInstanceContract;
use App\Telegram\Contracts\NotificationInstanceContract;
use App\Telegram\Contracts\UserInstanceContract;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Models\TelegramUser;
use Domain\TelegramBot\Services\BotManager;
use Illuminate\Support\Carbon;
use Nutgram\Laravel\RunningMode\LaravelWebhook;
use SergiX44\Nutgram\Nutgram;
use Support\Contracts\HumanDateParserContract;

if (!function_exists('menu')) {
    function menu(?string $botName = null): ?MenuContract
    {
        if (empty($botName)) {
            return app(MenuContract::class);
        }

        $factory = config("telegram_bot.menu.$botName");

        if (empty($factory)) {
            return null;
        }

        return app()->instance(MenuContract::class, $factory::create());
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
    function nutgram(?string $botName = null, bool $polling = false): Nutgram
    {
        if (empty($botName)) {
            return app(BotInstanceContract::class);
        }

        if ($polling) {
            $bot = app(Nutgram::class);
            return app()->instance(BotInstanceContract::class, $bot);
        }

        $bot = new Nutgram(config("nutgram.bots.$botName.token"));
        $bot->setRunningMode(LaravelWebhook::class);
        return app()->instance(BotInstanceContract::class, $bot);
    }
}

if (!function_exists('bot')) {
    function bot(): BotContract
    {
        return app(BotContract::class);
    }
}

if (!function_exists('init_bot')) {
    function init_bot(string $botName, bool $polling = false): Nutgram
    {
        $bot = nutgram($botName, $polling);
        app()->instance(BotContract::class, new BotManager($bot));
        tuser($botName);
        menu($botName);
        return $bot;
    }
}

if (!function_exists('notify'))
{
    function notify(string $message, Carbon $date): NotificationInstanceContract
    {
        /** @var NotificationInstanceContract $notify */
        $notify = app(NotificationInstanceContract::class);
        $notify->send($message, $date);
        return $notify;
    }
}

if (!function_exists('tuser')) {
    function tuser(?string $botName = null): UserStateContract
    {
        if (empty($botName)) {
            return app(UserInstanceContract::class);
        }

        /** @var UserStateContract $userState */
        $userState = app(UserStateContract::class);
        $userState->setBotName($botName);
        return app()->instance(UserInstanceContract::class, $userState);
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
