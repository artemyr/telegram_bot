<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('wedding');
})->name('home');

\Domain\Schedule\Routes\Routes::run();
\Domain\Travel\Routes\Routes::run();
