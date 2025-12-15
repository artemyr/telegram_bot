<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/categories')->name('categories');
Route::get('/categories/calendar')->name('calendar');
Route::get('/categories/food')->name('food');
Route::get('/settings')->name('settings');
Route::get('/settings/view')->name('view');
Route::get('/settings/constants')->name('constants');
