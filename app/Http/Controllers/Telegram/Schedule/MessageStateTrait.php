<?php

namespace App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Telegram\TelegramStateTrait;
use Domain\TelegramBot\Enum\LastMessageType;

class MessageStateTrait extends Controller
{
    use TelegramStateTrait;

    public function __invoke()
    {
        tuserstate()->changeLastMessageType(LastMessageType::USER_MESSAGE);
        $this->handleState();
    }
}
