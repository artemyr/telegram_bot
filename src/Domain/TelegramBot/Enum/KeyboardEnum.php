<?php

namespace Domain\TelegramBot\Enum;

enum KeyboardEnum: string
{
    case BACK = 'back';

    public function label(): string
    {
        return match($this) {
            self::BACK => '⏪ Назад',
        };
    }
}
