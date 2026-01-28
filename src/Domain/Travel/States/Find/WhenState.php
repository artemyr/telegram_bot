<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class WhenState extends AbstractState
{
    protected static array $when = [
        [
            "Сегодня",
            "Завтра"
        ],
        "Выбрать даты",
    ];

    public function render(): void
    {
        $keyboard = self::$when;
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Выбор даты",
            ])
            ->replyKeyboard($keyboard)
            ->send();
    }

    public function handle(): BotState
    {
        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, self::$when)) {
            message('Выберите из списка');
            return $this;
        }

        return new HowState();
    }
}
