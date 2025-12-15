<?php

namespace Domain\Calendar\Enum;

use Domain\Calendar\Actions\PiAction;
use Domain\Calendar\Actions\StartWorkAction;
use Domain\Calendar\Actions\WorkAction;

enum CalendarEnum: string
{
    case PI = '🚽 Отметить пись пись';
    case WORK = '🔨 Отметить начало трудовой сессии';
    case START_WORK = '🔨 Отметить приход на работу';

    public function action(): string
    {
        return match($this) {
            self::PI => PiAction::class,
            self::WORK => WorkAction::class,
            self::START_WORK => StartWorkAction::class,
        };
    }
}
