<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelFormat;

class HowState extends AbstractState
{
    protected static array $how = [];

    public function __construct(?string $path = null)
    {
        parent::__construct($path);

        TravelFormat::query()->get()->each(function (TravelFormat $travelFormat) {
            self::$how[] = $travelFormat->title;
        });
    }

    public function render(): void
    {
        $keyboard = self::$how;
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Выбор формата",
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


        if (!$this->validate($query, self::$how)) {
            message('Выберите из списка');
            return $this;
        }

        message('Онбординг');

        return new MenuBotState(troute('home'));
    }
}
