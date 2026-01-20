<?php

namespace App\Telegram\Factory;


use SergiX44\Nutgram\Nutgram;

class TravelBotFactory
{
    public function __invoke(): void
    {
        $bot = travel_bot();

        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage('Main bot');
        });

        $bot->run();
    }
}
