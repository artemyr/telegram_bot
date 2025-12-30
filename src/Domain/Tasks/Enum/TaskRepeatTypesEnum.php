<?php

namespace Domain\Tasks\Enum;

enum TaskRepeatTypesEnum: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case CUSTOM = 'custom';
}
