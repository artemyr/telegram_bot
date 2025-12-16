<?php

namespace App\Providers;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Services\KeyboardManager;
use Domain\TelegramBot\Services\UserStateManager;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KeyboardContract::class, KeyboardManager::class);
        $this->app->singleton(UserStateContract::class, UserStateManager::class);
    }

    public function boot(): void
    {
    }
}
