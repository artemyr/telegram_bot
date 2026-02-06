<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\States\AbstractState;

class SkillState extends AbstractState
{
    public function render(): void
    {
        $keyboard[] = '🟢 Новичок';
        $keyboard[] = '🔵 Средний';
        $keyboard[] = '🔴 Уверенный';
        $keyboard[] = '⚫ Эксперт';
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
            $level = match ($query) {
                '🟢 Новичок' => 'beginner',
                '🔵 Средний' => 'intermediate',
                '🔴 Уверенный' => 'confident',
                '⚫ Эксперт' => 'expert',
            };
            if (!empty($level)) {
                $questionnaire->level = $level;
                $questionnaire->save();
                return new StyleState();
            }
        }

        return $this;
    }
}
