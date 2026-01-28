<?php

namespace App\Providers;

use App\Models\User;
use App\Telegram\Contracts\NotificationInstanceContract;
use Domain\Schedule\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Schedule\Tasks\Services\RecurrenceTaskNotificationCreator;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\MessageContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Models\TelegramUser;
use Domain\TelegramBot\Services\KeyboardManager;
use Domain\TelegramBot\Services\MessageManager;
use Domain\TelegramBot\Services\NotificationManager;
use Domain\TelegramBot\Services\UserStateManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KeyboardContract::class, KeyboardManager::class);
        $this->app->singleton(MessageContract::class, MessageManager::class);
        $this->app->singleton(UserStateContract::class, UserStateManager::class);
        $this->app->singleton(NotificationInstanceContract::class, NotificationManager::class);
        $this->app->singleton(RecurrenceTaskNotificationCreatorContract::class, RecurrenceTaskNotificationCreator::class);
    }

    public function boot(): void
    {
        Gate::define('remove_telegram_hook', function (?User $user) {
            $tuserId = nutgram()->userId();

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
