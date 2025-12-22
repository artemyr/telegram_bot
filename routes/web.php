<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/calendar')->name('calendar');
Route::get('/calendar/add')->name('calendar.add');
Route::get('/calendar/list')->name('calendar.list');

Route::get('/tasks')->name('tasks');
Route::get('/tasks/add')->name('tasks.add');
Route::get('/tasks/list')->name('tasks.list');
Route::get('/tasks/recurrence/add')->name('tasks.recurrence.add');
Route::get('/tasks/recurrence/list')->name('tasks.recurrence.list');

Route::get('/food')->name('food');

Route::get('/settings')->name('settings');
Route::get('/settings/timezone')->name('timezone');
