<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\States\AbstractState;

class AgeState extends AbstractState
{
    public function render(): void
    {
        $keyboard[] = 'Не указывать';
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Укажите ваш возвраст",
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
            if (filter_var($query, FILTER_VALIDATE_INT)) {
                $questionnaire->age = $query;
                $questionnaire->save();
                return new GenderState();
            } else {
                message('Введите целое число');
            }
        }

        return $this;
    }
}
