<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\States\AbstractState;

class GenderState extends AbstractState
{
    public function render(): void
    {
        $keyboard[] = '👨 Мужской';
        $keyboard[] = '👩 Женский';
        $keyboard[] = 'Не указывать';
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Укажите ваш пол",
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

        if (!empty($query)) {
            $gender = match ($query) {
                '👨 Мужской' => 'male',
                '👩 Женский' => 'female',
            };
            if (!empty($gender)) {
                $questionnaire->gender = $gender;
                $questionnaire->save();
                return new SkillState();
            }
        }

        return $this;
    }
}
