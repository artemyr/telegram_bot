<?php

namespace App\Telegram\Factory;


use App\Http\Controllers\Telegram\Travel\StartController;
use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\CheckUserMiddleware;
use Support\Traits\Runable;

class TravelBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = bot();
        travel_user();

        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(CheckUserMiddleware::class);

        $bot->onCommand('start', StartController::class);

        $bot->run();
    }
}
