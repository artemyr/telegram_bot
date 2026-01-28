<?php

namespace App\Telegram\Factory;


use App\Http\Controllers\Telegram\Travel\CallbackStateController;
use App\Http\Controllers\Telegram\Travel\MessageStateController;
use App\Http\Controllers\Telegram\Travel\StartController;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\CheckUserMiddleware;
use Support\Traits\Runable;

class TravelBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = nutgram();

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->onCommand('start', StartController::class);

        $bot->onMessage(MessageStateController::class);

        $bot->onCallbackQuery(CallbackStateController::class);

        $bot->run();
    }
}
