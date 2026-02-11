<?php

namespace Domain\Travel\Routes;

use Support\Traits\Runable;
use Illuminate\Support\Facades\Route;

class Routes
{
    use Runable;

    public function handle()
    {
        Route::get('/travel/find')->name('travel.find');
        Route::get('/travel/create')->name('travel.create');
        Route::get('/travel/profile')->name('travel.profile');
        Route::get('/travel/how_work')->name('travel.how_work');
    }
}
