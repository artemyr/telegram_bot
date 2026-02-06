<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\States\AbstractState;

class StyleState extends AbstractState
{
    public function render(): void
    {
        $keyboard[] = '🏂 Трассы';
        $keyboard[] = '❄️ Фрирайд';
        $keyboard[] = '🎢 Парк';
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

//        if (!empty($query)) {
//            $level = match ($query) {
//                '🏂 Трассы' => 'beginner',
//                '❄️ Фрирайд' => 'intermediate',
//                '🎢 Парк' => 'confident',
//            };
//            if (!empty($gender)) {
//                $questionnaire->level = $level;
//                $questionnaire->save();
//                return new StyleState();
//            }
//        }

        return $this;
    }
}
