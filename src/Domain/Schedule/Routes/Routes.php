<?php

namespace Domain\Schedule\Routes;

use Support\Traits\Runable;
use Illuminate\Support\Facades\Route;


class Routes
{
    use Runable;

    public function handle()
    {
        Route::get('/schedule/calendar')->name('calendar');
        Route::get('/schedule/calendar/add')->name('calendar.add');
        Route::get('/schedule/calendar/list')->name('calendar.list');

        Route::get('/schedule/tasks')->name('tasks');
        Route::get('/schedule/tasks/add')->name('tasks.add');
        Route::get('/schedule/tasks/list')->name('tasks.list');
        Route::get('/schedule/tasks/recurrence/add')->name('tasks.recurrence.add');
        Route::get('/schedule/tasks/recurrence/list')->name('tasks.recurrence.list');

        Route::get('/schedule/food')->name('food');
        Route::get('/schedule/food/to_buy')->name('food.to_buy');
        Route::get('/schedule/food/spoil')->name('food.spoil');
        Route::get('/schedule/food/add')->name('food.add');
        Route::get('/schedule/food/list')->name('food.list');

        Route::get('/schedule/settings')->name('settings');
        Route::get('/schedule/settings/work/start')->name('work.start');
        Route::get('/schedule/settings/work/end')->name('work.end');
        Route::get('/schedule/settings/work/test')->name('work.test');
        Route::get('/schedule/settings/notifications/recreate')->name('notifications.recreate');
        Route::get('/schedule/settings/timezone')->name('timezone');
        Route::get('/schedule/settings/webhook/off')->name('webhook_off');
    }
}
