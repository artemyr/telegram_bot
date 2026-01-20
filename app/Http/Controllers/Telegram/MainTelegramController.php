<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\Factory\ScheduleBotFactory;
use App\Telegram\Factory\TravelBotFactory;

class MainTelegramController extends Controller
{
    public function schedule(): void
    {
        logger()->debug('schedule hook income');
        $bot = new ScheduleBotFactory();
        $bot();
    }

    public function travel(): void
    {
        logger()->debug('travel hook income');
        $bot = new TravelBotFactory();
        $bot();
    }
}
