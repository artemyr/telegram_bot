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
Route::get('/food/to_buy')->name('food.to_buy');
Route::get('/food/spoil')->name('food.spoil');

Route::get('/settings')->name('settings');
Route::get('/settings/work/start')->name('work.start');
Route::get('/settings/work/end')->name('work.end');
Route::get('/settings/notifications/recreate')->name('notifications.recreate');
Route::get('/settings/timezone')->name('timezone');
Route::get('/settings/webhook/off')->name('webhook_off');
