<?php

namespace App\Http\Controllers\Telegram\Travel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Telegram\TelegramStateTrait;

class CallbackStateController extends Controller
{
    use TelegramStateTrait;

    public function __invoke()
    {
        $this->handleState();
    }
}
