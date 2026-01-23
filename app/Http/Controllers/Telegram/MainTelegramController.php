<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\Factory\ScheduleBotFactory;
use App\Telegram\Factory\TravelBotFactory;

class MainTelegramController extends Controller
{
    public function handle(string $bot): void
    {
        if (method_exists($this, $bot)) {
            $this->{$bot}();
        }
    }

    protected function schedule(): void
    {
        ScheduleBotFactory::run();
    }

    protected function travel(): void
    {
        TravelBotFactory::run();
    }
}
