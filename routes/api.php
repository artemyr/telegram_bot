<?php

use App\Http\Controllers\Telegram\MainTelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhook/schedule', [MainTelegramController::class, 'schedule']);
Route::post('/webhook/travel', [MainTelegramController::class, 'travel']);
