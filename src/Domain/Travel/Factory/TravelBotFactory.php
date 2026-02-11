<?php

namespace Domain\Travel\Factory;


use App\Http\Controllers\Telegram\Travel\CallbackStateController;
use App\Http\Controllers\Telegram\Travel\MessageStateController;
use App\Http\Controllers\Telegram\Travel\StartController;
use Domain\TelegramBot\Middleware\AuthMiddleware;
use Domain\TelegramBot\Middleware\CheckUserMiddleware;
use Support\Traits\Runable;

class TravelBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = nutgram();

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->registerCommand(StartController::class);

        $bot->onMessage(MessageStateController::class);

        $bot->onCallbackQuery(CallbackStateController::class);
    }
}
