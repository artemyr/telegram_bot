<?php

namespace App\Telegram\Factory;

use App\Http\Controllers\Telegram\Schedule\CallbackController;
use App\Http\Controllers\Telegram\Schedule\MessageController;
use App\Http\Controllers\Telegram\Schedule\StartController;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\CheckUserMiddleware;

class ScheduleBotFactory
{
    public function __invoke(): void
    {
        $bot = schedule_bot();

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->onCommand('start', StartController::class);

        $bot->onMessage(MessageController::class);

        $bot->onCallbackQuery(CallbackController::class);

        $bot->run();
    }
}
