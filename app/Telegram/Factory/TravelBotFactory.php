<?php

namespace App\Telegram\Factory;


use App\Http\Controllers\Telegram\Travel\StartController;
use Support\Traits\Runable;

class TravelBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = travel_bot();
        travel_user();

        $bot->onCommand('start', StartController::class);

        $bot->run();
    }
}
