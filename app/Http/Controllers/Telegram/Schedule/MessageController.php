<?php

namespace App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Telegram\AbstractTelegramController;
use Domain\TelegramBot\Enum\LastMessageType;

class MessageController extends AbstractTelegramController
{
    public function __invoke()
    {
        tuserstate()->changeLastMessageType(LastMessageType::USER_MESSAGE);

        $this->try(function () {
            $this->handleState();
        });
    }
}
