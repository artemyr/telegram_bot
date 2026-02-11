<?php

namespace Domain\TelegramBot\Enum;

enum KeyboardEnum: string
{
    case BACK = 'back';
    case NEXT = 'next';
    case PREV = 'prev';

    public function label(): string
    {
        return match ($this) {
            self::BACK => '⏪ Вернуться',
            self::PREV => '◀️ Назад',
            self::NEXT => 'Дальше ➡️',
        };
    }
}
