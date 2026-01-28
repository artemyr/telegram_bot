<?php

namespace Domain\Schedule\Settings\States;

use Domain\Schedule\Settings\Enums\TimezoneEnum;
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
        ])->replyKeyboard($keyboard)
            ->removeLast()
            ->send();
    }

    public function handle(): void
    {
        if (nutgram()->message()->getText() === KeyboardEnum::BACK->label()) {
            $newState = new MenuBotState(troute('settings'));
            tuser()->changeState($newState);
            return;
        }

        foreach (TimezoneEnum::cases() as $case) {
            if (nutgram()->message()->getText() === $case->value) {
                message("Вы отметили: " . $case->value);
                TelegramUser::query()
                    ->where('telegram_id', nutgram()->userId())
                    ->update([
                        'timezone' => $case->value,
                    ]);
            }
        }

        $newState = new TimezoneState();
        tuser()->changeState($newState);
    }
}
