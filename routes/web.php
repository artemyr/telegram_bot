<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/calendar')->name('calendar');
Route::get('/calendar/add')->name('calendar.add');
Route::get('/calendar/list')->name('calendar.list');

Route::get('/food')->name('food');

Route::get('/settings')->name('settings');
Route::get('/settings/timezone')->name('timezone');
