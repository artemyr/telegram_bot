<?php

namespace Domain\Travel\Enum;

enum LevelEnum: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case CONFIDENT = 'confident';
    case EXPERT = 'expert';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => '🟢 Новичок',
            self::INTERMEDIATE => '🔵 Средний',
            self::CONFIDENT => '🔴 Уверенный',
            self::EXPERT => '⚫ Эксперт',
        };
    }

    public static function tryFromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }
}
