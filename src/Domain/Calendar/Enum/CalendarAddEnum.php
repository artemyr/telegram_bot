<?php

namespace Domain\Calendar\Enum;

use Domain\Calendar\Actions\StartWorkAction;
use Domain\Calendar\Actions\WorkSessionAction;

enum CalendarAddEnum: string
{
    case WORK = 'work';
    case START_WORK = 'start_work';

    public function action(): string
    {
        return match ($this) {
            self::WORK => WorkSessionAction::class,
            self::START_WORK => StartWorkAction::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::WORK => '⏳ начало трудовой сессии',
            self::START_WORK => '🔨 приход на работу',
        };
    }
}
