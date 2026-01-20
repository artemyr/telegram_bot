<?php

namespace App\Providers;

use App\Models\User;
use App\Telegram\Contracts\ScheduleBotContract;
use App\Telegram\Contracts\TravelBotContract;
use Domain\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Tasks\Services\RecurrenceTaskNotificationCreator;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Models\TelegramUser;
use Domain\TelegramBot\Services\KeyboardManager;
use Domain\TelegramBot\Services\MessageManager;
use Domain\TelegramBot\Services\UserStateManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Nutgram\Laravel\RunningMode\LaravelWebhook;
use SergiX44\Nutgram\Nutgram;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KeyboardContract::class, KeyboardManager::class);
        $this->app->singleton(UserStateContract::class, UserStateManager::class);
        $this->app->singleton(MessageContract::class, MessageManager::class);
        $this->app->singleton(RecurrenceTaskNotificationCreatorContract::class, RecurrenceTaskNotificationCreator::class);

        $this->app->singleton(ScheduleBotContract::class, function () {
            $bot = new Nutgram(config('nutgram.bots.schedule'));
            $bot->setRunningMode(LaravelWebhook::class);
            return $bot;
        });
        $this->app->singleton(TravelBotContract::class, function () {
            $bot = new Nutgram(config('nutgram.bots.travel'));
            $bot->setRunningMode(LaravelWebhook::class);
            return $bot;
        });
    }

    public function boot(): void
    {
        Gate::define('remove_telegram_hook', function (?User $user) {
            $tuserId = schedule_bot()->userId();

            if (empty($tuserId)) {
                return false;
            }

            $tuser = TelegramUser::query()
                ->with('user')
                ->where('telegram_id', $tuserId)
                ->first();

            if (empty($tuser) || empty($tuser->user)) {
                return false;
            }

            $user = $tuser->user;

            if ($user->role !== 'admin') {
                return false;
            }

            return true;
        });
    }
}
