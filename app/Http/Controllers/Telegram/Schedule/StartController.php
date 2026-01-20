<?php

namespace App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Telegram\AbstractTelegramController;
use Domain\TelegramBot\MenuBotState;

class StartController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            logger()->debug('schedule start command invoke');

            $userDto = tuser();
            $state = $userDto->state ?? new MenuBotState();
            $state->render();
        });
    }
}
