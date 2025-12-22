<?php

namespace Domain\Settings\States;

use Domain\Settings\Enums\TimezoneEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\Models\TelegramUser;

class TimezoneState extends BotState
{
    public bool $silent = true;

    public function render(): void
    {
        $keyboard = [
            KeyboardContract::BACK
        ];

        foreach (TimezoneEnum::cases() as $case) {
            $keyboard[] = $case->value;
        }

        $timezone = tusertimezone();

        Keyboard::send("Раздел: Настройки\nВаш часовой пояс: $timezone\nВыберите часовой пояс:", $keyboard);
    }

    public function handle(): ?BotState
    {
        if (bot()->message()->getText() === KeyboardContract::BACK) {
            $newState = new MenuBotState(troute('settings'));
            UserState::changeState(bot()->userId(), $newState);
            return $newState;
        }

        foreach (TimezoneEnum::cases() as $case) {
            if (bot()->message()->getText() === $case->value) {
                send("Вы отметили: " . $case->value);
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
