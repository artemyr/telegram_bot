<?php

use App\Http\Controllers\Telegram\MainTelegramController;
use App\Http\Controllers\WorkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhook/{bot}', [MainTelegramController::class, 'handle']);

Route::get('/config', [WorkController::class, 'config']);
