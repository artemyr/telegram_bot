<?php

namespace App\Http\Controllers\Telegram;

use Domain\TelegramBot\MenuBotState;

class StartController extends AbstractTelegramController
{
    public function __invoke()
    {
        $this->try(function () {
            $userDto = tuser();

            if (config('telegram_bot.debug', false)) {
                message()->text("Состояние пользователя: \n```\n$userDto\n```")->send();
            }

            $state = $userDto->state ?? new MenuBotState();
            $state->render();
        });
    }
}
