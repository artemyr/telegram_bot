<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelClaim;
use Domain\Travel\Models\TravelFormat;

class HowState extends AbstractState
{
    protected static array $how = [];
    protected ?TravelClaim $claim;

    private function getItems(): array
    {
        if (empty(self::$how)) {
            TravelFormat::query()
                ->get()
                ->each(function (TravelFormat $travelFormat) {
                    self::$how[] = $travelFormat->title;
                });
        }

        return self::$how;
    }

    private function getClaim(): ?TravelClaim
    {
        if (empty($this->claim)) {
            $this->claim = TravelClaim::query()
                ->where('telegram_user_id', nutgram()->userId())
                ->first();
        }

        return $this->claim;
    }

    public function render(): void
    {
        $keyboard = $this->getItems();
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
        if (empty($this->getClaim())) {
            message('Ваша заявка потеряна. Начните заного');
            return new WhereState();
        }

        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, $this->getItems())) {
            message('Выберите из списка');
            return $this;
        }

        $format = TravelFormat::query()
            ->select('id')
            ->where('title', $query)
            ->first();

        if ($format) {
            $this->getClaim()->travel_format_id = $format->id;
            $this->getClaim()->save();
        }

        $claim = TravelClaim::query()
            ->select(['id', 'telegram_user_id','date_from','date_to'])
            ->where('telegram_user_id', nutgram()->userId())
            ->with(['travelFormat','travelResort'])
            ->first();

        message()
            ->text([
                "Где: {$claim->format->title}",
                "Когда: $claim->date_from $claim->date_to",
                "Как: {$claim->resort->title}",
            ])
            ->send();

        return new MenuBotState(troute('home'));
    }
}
