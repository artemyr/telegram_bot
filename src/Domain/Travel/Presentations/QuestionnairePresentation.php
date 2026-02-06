<?php

namespace Domain\Travel\Presentations;

use Domain\Travel\Enum\GenderEnum;
use Domain\Travel\Enum\LevelEnum;
use Domain\Travel\Models\TravelQuestionnaire;
use Support\Traits\Makeable;

class QuestionnairePresentation
{
    use Makeable;

    public function __construct(protected TravelQuestionnaire $questionnaire)
    {
    }

    public function textMessage(): string
    {
        $text = [];

        $name = $this->questionnaire->name;
        $age = $this->questionnaire->age;

        $gender = $this->questionnaire->gender;
        $gender = GenderEnum::tryFrom($gender)?->label();

        $level = $this->questionnaire->level;
        $level = LevelEnum::tryFrom($level)?->label();

        $collectionStyles = $this->questionnaire->travelStyles;

        $styles = [];
        foreach ($collectionStyles as $style) {
            $styles[] = $style->title;
        }
        $styles = implode(', ', $styles);

        $text[] = "Имя: " . ($name ?: '...');
        $text[] = "Возвраст: " . ($age ?: '...');
        $text[] = "Пол: " . ($gender ?: '...');
        $text[] = "Уровень катания: " . ($level ?: '...');
        $text[] = "Стиль катания: " . ($styles ?: '...');

        return implode("\n", $text);
    }
}
