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
    protected ?TravelClaim $claim;

    public function __construct(?string $path = null)
    {
        parent::__construct($path);

        $this->claim = TravelClaim::query()
            ->where('telegram_user_id', nutgram()->userId())
            ->first();
    }

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
        if (empty($this->claim)) {
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

        $this->claim->date_from = $now->startOfDay();
        $this->claim->date_to = $now->endOfDay();

        $this->claim->save();
    }

    protected function tomorrow(): void
    {
        $tomorrow = now(tusertimezone())->addDay();

        $this->claim->date_from = $tomorrow->startOfDay();
        $this->claim->date_to = $tomorrow->endOfDay();

        $this->claim->save();
    }
}
