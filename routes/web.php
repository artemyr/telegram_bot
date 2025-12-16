<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/calendar')->name('calendar');
Route::get('/calendar/add')->name('calendar.add');
Route::get('/calendar/list')->name('calendar.list');

Route::get('/food')->name('food');
