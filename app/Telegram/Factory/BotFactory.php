<?php

namespace App\Telegram\Factory;

use App\Http\Controllers\Telegram\CallbackController;
use App\Http\Controllers\Telegram\MessageController;
use App\Http\Controllers\Telegram\StartController;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\CheckUserMiddleware;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);
        bot()->middleware(CheckUserMiddleware::class);
        bot()->onCommand('start', StartController::class);
        bot()->onMessage(MessageController::class);
        bot()->onCallbackQuery(CallbackController::class);
    }
}
