<?php

namespace App\Http\Controllers\Telegram;

class MessageController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            $this->handleState();
        });
    }
}
