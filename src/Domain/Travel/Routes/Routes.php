<?php

namespace Domain\Travel\Routes;

use Support\Traits\Runable;
use Illuminate\Support\Facades\Route;


class Routes
{
    use Runable;

    public function handle()
    {
        Route::get('/travel/find')->name('travel_find');
        Route::get('/travel/create')->name('travel_create');
        Route::get('/travel/profile')->name('travel_profile');
        Route::get('/travel/how_work')->name('travel_how_work');
    }
}
