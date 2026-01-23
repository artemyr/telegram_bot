<?php

namespace App\Providers;

use Domain\Schedule\Calendar\Providers\CalendarServiceProvider;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(CalendarServiceProvider::class);
    }

    public function boot(): void
    {
    }
}
