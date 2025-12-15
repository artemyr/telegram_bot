<?php

namespace Domain\Calendar\Providers;

use Illuminate\Support\ServiceProvider;

class CalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(ActionsServiceProvider::class);
    }

    public function boot(): void
    {
    }
}
