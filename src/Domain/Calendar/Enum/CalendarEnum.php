<?php

namespace Domain\Calendar\Enum;

enum CalendarEnum: string
{
    case PI = '🚽 Отметить пись пись';
    case WORK = '🔨 Отметить начало трудовой сессии';
    case START_WORK = '🔨 Отметить приход на работу';
}
