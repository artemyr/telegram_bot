<?php

namespace Domain\Schedule\Routes;

use Support\Traits\Runable;
use Illuminate\Support\Facades\Route;

class Routes
{
    use Runable;

    public function handle()
    {
        Route::get('/schedule/calendar')->name('schedule.calendar');
        Route::get('/schedule/tasks')->name('schedule.tasks');
        Route::get('/schedule/food')->name('schedule.food');
        Route::get('/schedule/settings')->name('schedule.settings');
    }
}
