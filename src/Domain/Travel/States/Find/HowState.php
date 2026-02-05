<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelFormat;
use Domain\Travel\Presentations\ClaimPresentation;
use Domain\Travel\States\AbstractState;
use Domain\Travel\States\Questionnaire\NameState;

class HowState extends AbstractState
{
    protected static array $how = [];

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
        $claim = $this->getClaim();

        if (empty($claim)) {
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
            $claim->travel_format_id = $format->id;
            $claim->save();
        }

        $claim = $this->getClaim();

        message()
            ->text([
                "Ваши параметры поиска:",
                ClaimPresentation::make($claim)->textMessage()
            ])
            ->send();

        return new NameState();
    }
}
