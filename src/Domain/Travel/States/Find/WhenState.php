<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelClaim;

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
        $claim = $this->getClaim();

        if (empty($claim)) {
            message('Ваша заявка потеряна. Начните заного');
            return new WhereState();
        }

        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, self::$when)) {
            message('Выберите из списка');
            return $this;
        }

        match ($query) {
            'Сегодня' => $this->today(),
            'Завтра' => $this->tomorrow(),
        };

        return new HowState();
    }

    protected function today(): void
    {
        $now = now(tusertimezone());

        $claim = $this->getClaim();

        $claim->date_from = $now->startOfDay();
        $claim->date_to = $now->endOfDay();

        $claim->save();
    }

    protected function tomorrow(): void
    {
        $tomorrow = now(tusertimezone())->addDay();

        $claim = $this->getClaim();

        $claim->date_from = $tomorrow->startOfDay();
        $claim->date_to = $tomorrow->endOfDay();

        $claim->save();
    }
}
