<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;

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
        init_bot('schedule')->run();
    }

    protected function travel(): void
    {
        init_bot('travel')->run();
    }
}
