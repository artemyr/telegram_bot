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
    public function render(): void
    {
        $keyboard = [
            KeyboardContract::BACK
        ];

        foreach (CalendarAddEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        send([
            "Раздел: Календарь",
            "Выберите что хотите отметить:"
        ], Keyboard::markup($keyboard));
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('calendar'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        foreach (CalendarAddEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                send("Вы отметили: " . $case->value);
                $action = new ($case->action());
                $action();
            }
        }

        return new CalendarAddState();
    }
}
