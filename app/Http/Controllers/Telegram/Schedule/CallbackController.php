<?php

namespace App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Telegram\AbstractTelegramController;

class CallbackController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            $this->handleState();
        });
    }
}
