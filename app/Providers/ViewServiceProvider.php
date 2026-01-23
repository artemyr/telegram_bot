<?php

namespace App\Providers;

use App\Menu\MenuItem;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        MenuItem::setDefaultState(MenuBotState::class);
    }

    public function boot(): void
    {
    }
}
