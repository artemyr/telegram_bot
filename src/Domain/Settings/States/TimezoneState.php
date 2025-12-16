<?php

namespace Domain\Settings\States;

use Domain\Settings\Enums\TimezoneEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\MenuEnum;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;

class TimezoneState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $keyboard = [
            MenuEnum::BACK->value
        ];

        foreach (TimezoneEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        $timezone = config('app.timezone');

        Keyboard::send("Раздел: Настройки\nВаш часовой пояс: $timezone\nВыберите часовой пояс:", $keyboard);
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === MenuEnum::BACK->value) {

            Keyboard::remove();

            request()->merge([
                'path' => troute('settings')
            ]);

            return new MenuBotState();
        }

        foreach (TimezoneEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                bot()->sendMessage("Вы отметили: " . $case->value);
                UserState::changeTimezone(bot()->userId(), $case->value);
                config(['app.timezone' => $case->value]);
            }
        }

        return new TimezoneState();
    }
}
