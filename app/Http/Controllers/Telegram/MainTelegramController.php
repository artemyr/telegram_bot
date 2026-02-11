<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Domain\Schedule\Factory\ScheduleBotFactory;
use Domain\Travel\Factory\TravelBotFactory;

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
        init_bot(ScheduleBotFactory::class)->run();
    }

    protected function travel(): void
    {
        init_bot(TravelBotFactory::class)->run();
    }
}
