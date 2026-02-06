<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Models\TravelStyle;
use Domain\Travel\Presentations\QuestionnairePresentation;
use Domain\Travel\States\AbstractState;
use Illuminate\Support\Collection;

class StyleState extends AbstractState
{
    private function getKeyboard(): array
    {
        $result = [];

        $questionnaire = $this->getQuestionnaire();
        $styles = $questionnaire->travelStyles->pluck('id');

        TravelStyle::query()
            ->whereNotIn('id', $styles)
            ->chunk(2, function (Collection $items) use (&$result) {
                if ($items->count() > 1) {
                    $result[] = [
                        $items->first()->title,
                        $items->last()->title,
                    ];
                } else {
                    $result[] = $items->first()->title;
                }
            });


        return $result;
    }

    public function render(): void
    {
        $questionnaire = $this->getQuestionnaire();
        $styles = $questionnaire->travelStyles;

        $keyboard = $this->getKeyboard();

        if ($styles->isEmpty()) {
            $keyboard[] = 'Отметить все';
            $keyboard[] = 'Не указывать';
        }

        if ($styles->isNotEmpty()) {
            $keyboard[] = 'Далее';
        }

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

        if ($query === "Далее") {
            $questionnaire = $this->getQuestionnaire();

            message()
                ->text([
                    "Вы заполнили анкету",
                    QuestionnairePresentation::make($questionnaire)->textMessage(),
                    "Теперь вам будут поступать предложения",
                ])->send();
            return new MenuBotState(troute('home'));
        }

        if ($query === "Не указывать") {
            return new MenuBotState(troute('home'));
        }

        if (!$this->validate($query, $this->getKeyboard())) {
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
