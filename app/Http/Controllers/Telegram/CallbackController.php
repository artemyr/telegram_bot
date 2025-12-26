<?php

namespace App\Http\Controllers\Telegram;

class CallbackController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            $this->handleState();
        });
    }
}
