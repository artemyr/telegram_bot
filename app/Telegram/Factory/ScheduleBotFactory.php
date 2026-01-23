<?php

namespace App\Telegram\Factory;

use App\Http\Controllers\Telegram\Schedule\CallbackStateTrait;
use App\Http\Controllers\Telegram\Schedule\MessageStateTrait;
use App\Http\Controllers\Telegram\Schedule\StartController;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\CheckUserMiddleware;
use Support\Traits\Runable;

class ScheduleBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = schedule_bot();
        schedule_user();

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->onCommand('start', StartController::class);

        $bot->onMessage(MessageStateTrait::class);

        $bot->onCallbackQuery(CallbackStateTrait::class);

        $bot->run();
    }
}
