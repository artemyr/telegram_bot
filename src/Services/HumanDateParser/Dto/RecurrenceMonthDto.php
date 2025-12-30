<?php

namespace Services\HumanDateParser\Dto;

use Carbon\Carbon;

readonly class RecurrenceMonthDto
{
    public function __construct(
        public Carbon $time,
        public array  $daysOfMonth,
    )
    {
    }
}
