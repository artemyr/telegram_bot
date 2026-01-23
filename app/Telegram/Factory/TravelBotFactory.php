<?php

namespace App\Telegram\Factory;


use SergiX44\Nutgram\Nutgram;
use Support\Traits\Runable;

class TravelBotFactory
{
    use Runable;

    public function handle(): void
    {
        $bot = travel_bot();

        $bot->onCommand('start', );

        $bot->run();
    }
}
