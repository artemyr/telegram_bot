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
        Route::get('/travel/find/khutor')->name('travel_khutor');
        Route::get('/travel/find/red')->name('travel_red');
        Route::get('/travel/find/gas')->name('travel_gas');
        Route::get('/travel/find/sher')->name('travel_sher');
        Route::get('/travel/find/other')->name('travel_other');

        Route::get('/travel/create')->name('travel_create');
        Route::get('/travel/profile')->name('travel_profile');
        Route::get('/travel/how_work')->name('travel_how_work');
    }
}
