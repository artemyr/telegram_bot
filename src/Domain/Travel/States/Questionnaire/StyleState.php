<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelStyle;
use Domain\Travel\States\AbstractState;
use Illuminate\Support\Collection;

class StyleState extends AbstractState
{
    protected static array $items = [];

    private function getItems(): array
    {
        if (empty( self::$items)) {
            TravelStyle::query()
                ->chunk(2, function (Collection $items) {
                    self::$items[] = [
                        $items->first()->title,
                        $items->last()->title,
                    ];
                });
        }

        return self::$items;
    }

    public function render(): void
    {
        $keyboard = $this->getItems();
        $keyboard[] = 'Отметить все';
        $keyboard[] = 'Не указывать';
        $keyboard[] = 'Далее';
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Ваш стиль катания",
                "Можно выбрать несколько вариантов",
            ])
            ->replyKeyboard($keyboard)
            ->send();
    }

    public function handle(): BotState
    {
        $questionnaire = $this->getQuestionnaire();

        if (empty($questionnaire)) {
            message('Ваша анкета потеряна. Начните заного');
            return new NameState();
        }

        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if ($query === "Не указывать") {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, $this->getItems())) {
            message('Выберите из списка');
            return $this;
        }

        if (!empty($query)) {
            $style = TravelStyle::query()
                ->where('title', $query)
                ->first();

            if (!empty($style)) {
                $questionnaire = $this->getQuestionnaire();
                $questionnaire->travelStyles()->attach($style->id);
            }
        }

        return $this;
    }
}
