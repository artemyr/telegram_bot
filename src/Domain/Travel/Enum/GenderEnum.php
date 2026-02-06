<?php

namespace Domain\Travel\Enum;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function label(): string
    {
        return match ($this) {
            self::MALE => '👨 Мужской',
            self::FEMALE => '👩 Женский',
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
