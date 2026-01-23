<?php

namespace Domain\Calendar\States;

use Domain\Calendar\Enum\CalendarAddEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class CalendarAddState extends BotState
{
    public function render(): void
    {
        $keyboard = keyboard()->back();

        foreach (CalendarAddEnum::cases() as $case) {
            $keyboard[$case->value] = $case->label();
        }

        message()
            ->text([
                "Раздел: Календарь",
                "Выберите что хотите отметить:"
            ])->inlineKeyboard($keyboard)
            ->send();
    }

    public function handle(): void
    {
        if (!bot()->isCallbackQuery()) {
            message('Используйте кнопки для навигации');
            return;
        }

        $text = bot()->callbackQuery()->data;

        if ($text === KeyboardEnum::BACK->value) {
            keyboard()->remove();
            $newState = new MenuBotState(troute('calendar'));
            tuser()->changeState($newState);
            return;
        }

        foreach (CalendarAddEnum::cases() as $case) {
            if ($text === $case->value) {
                message("Вы отметили: " . $case->label());
                $action = new ($case->action());
                $action();
            }
        }
    }
}
