<?php

namespace Domain\Settings\States;

use Domain\Settings\Enums\TimezoneEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\Models\TelegramUser;

class TimezoneState extends BotState
{
    public function render(): void
    {
        $keyboard = [
            KeyboardEnum::BACK->label()
        ];

        foreach (TimezoneEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        $timezone = tusertimezone();

        message()->text([
            "Раздел: Настройки",
            "Ваш часовой пояс: $timezone",
            "Выберите часовой пояс:"
        ])->replyKeyboard($keyboard)->send();
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardEnum::BACK->label()) {
            $newState = new MenuBotState(troute('settings'));
            tuserstate()->changeState($newState);
            return $newState;
        }

        foreach (TimezoneEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                message("Вы отметили: " . $case->value);
                TelegramUser::query()
                    ->where('telegram_id', bot()->userId())
                    ->update([
                        'timezone' => $case->value,
                    ]);
            }
        }

        return new TimezoneState();
    }
}
