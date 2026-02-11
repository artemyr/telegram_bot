<?php

namespace Domain\Schedule\Factory;

use App\Http\Controllers\Telegram\Schedule\CallbackStateController;
use App\Http\Controllers\Telegram\Schedule\MessageStateController;
use App\Http\Controllers\Telegram\Schedule\StartController;
use Domain\TelegramBot\Middleware\AuthMiddleware;
use Domain\TelegramBot\Middleware\CheckUserMiddleware;
use Support\Traits\Runable;

class ScheduleBotFactory
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
