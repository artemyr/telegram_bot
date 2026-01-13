<?php

namespace App\Http\Controllers\Telegram;

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
