<?php

namespace App\Telegram\States;

use Domain\Calendar\Enum\CalendarEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\MenuEnum;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\MenuBotState;

class CalendarState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $keyboard = [
            MenuEnum::BACK->value
        ];

        foreach (CalendarEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        Keyboard::send("Раздел: Календарь\nВыберите что хотите сделать", $keyboard);
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === MenuEnum::BACK->value) {

            Keyboard::remove();

            request()->merge([
                'path' => troute('categories')
            ]);

            return new MenuBotState();
        }

        foreach (CalendarEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                bot()->sendMessage("Вы отметили: " . $case->value);
                $action = new ($case->action());
                $action();
            }
        }

        return new CalendarState();
    }
}
