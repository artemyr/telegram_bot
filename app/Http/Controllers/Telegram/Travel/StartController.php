<?php

namespace App\Http\Controllers\Telegram\Travel;

use App\Http\Controllers\Telegram\AbstractTelegramController;

class StartController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            message()->text("бот путешествий")->send();
        });
    }
}
