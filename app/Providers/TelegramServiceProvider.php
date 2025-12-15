<?php

namespace App\Providers;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Services\KeyboardManager;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KeyboardContract::class, KeyboardManager::class);
    }

    public function boot(): void
    {
    }
}
