<?php

namespace App\Http\Controllers\Telegram\Travel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Telegram\TelegramStateTrait;
use Domain\TelegramBot\Enum\LastMessageType;

class MessageStateController extends Controller
{
    use TelegramStateTrait;

    public function __invoke()
    {
        tuser()->changeLastMessageType(LastMessageType::USER_MESSAGE);
        $this->handleState();
    }
}
