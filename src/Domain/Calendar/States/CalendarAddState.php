<?php

namespace Domain\Calendar\States;

use Domain\Calendar\Enum\CalendarAddEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class CalendarAddState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $keyboard = [
            KeyboardContract::BACK
        ];

        foreach (CalendarAddEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        Keyboard::send("Раздел: Календарь\nВыберите что хотите отметить:", $keyboard);
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            UserState::changePath(bot()->userId(), troute('calendar'));

            return new MenuBotState();
        }

        foreach (CalendarAddEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                bot()->sendMessage("Вы отметили: " . $case->value);
                $action = new ($case->action());
                $action();
            }
        }

        return new CalendarAddState();
    }
}
