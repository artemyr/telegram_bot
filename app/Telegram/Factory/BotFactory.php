<?php

namespace App\Telegram\Factory;

use App\Http\Controllers\Telegram\MessageController;
use App\Http\Controllers\Telegram\StartController;
use App\Telegram\Middleware\AuthMiddleware;

class BotFactory
{
    public function __invoke(): void
    {
        bot()->middleware(AuthMiddleware::class);
        bot()->onCommand('start', StartController::class);
        bot()->onMessage(MessageController::class);
    }
}
