<?php

namespace Domain\Travel\Routes;

use Support\Traits\Runable;
use Illuminate\Support\Facades\Route;


class Routes
{
    use Runable;

    public function handle()
    {
        Route::get('/travel/find')->name('find');
        Route::get('/travel/create')->name('create');
        Route::get('/travel/profile')->name('profile');
        Route::get('/travel/how_work')->name('how_work');
    }
}
