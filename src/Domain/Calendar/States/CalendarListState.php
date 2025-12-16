<?php

namespace Domain\Calendar\States;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\MenuEnum;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class CalendarListState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $userDto = UserState::load(bot()->userId());

        $list = '';
        $num = 1;

        foreach ($userDto->actions as $action) {
            $list .= "$num) $action->title: $action->startDate\n";
            $num++;
        }

        if (empty($list)) {
            $list = 'Пусто...';
        }

        Keyboard::back("Раздел: Календарь\nСписок событий:\n$list");
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === MenuEnum::BACK->value) {

            Keyboard::remove();

            request()->merge([
                'path' => troute('calendar')
            ]);

            return new MenuBotState();
        }

        return new CalendarListState();
    }
}
