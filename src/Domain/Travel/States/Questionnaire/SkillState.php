<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Enum\LevelEnum;
use Domain\Travel\States\AbstractState;

class SkillState extends AbstractState
{
    public function render(): void
    {
        foreach (LevelEnum::cases() as $case) {
            $keyboard[] = $case->label();
        }

        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Ваш уровень катания",
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
            $level = LevelEnum::tryFromLabel($query);
            if (!empty($level)) {
                $questionnaire->level = $level->value;
                $questionnaire->save();

                $questionnaire->travelStyles()->detach();

                return new StyleState();
            }
        }

        return $this;
    }
}
