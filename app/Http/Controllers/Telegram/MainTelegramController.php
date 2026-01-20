<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\Factory\ScheduleBotFactory;
use App\Telegram\Factory\TravelBotFactory;

class MainTelegramController extends Controller
{
    public function schedule(): void
    {
        $bot = new ScheduleBotFactory();
        $bot();
    }

    public function travel(): void
    {
        $bot = new TravelBotFactory();
        $bot();
    }
}
