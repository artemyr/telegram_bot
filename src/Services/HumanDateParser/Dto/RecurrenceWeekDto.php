<?php

namespace Services\HumanDateParser\Dto;

use Carbon\Carbon;

readonly class RecurrenceWeekDto
{
    public function __construct(
        public Carbon $time,
        public array  $daysOfWeek,
    )
    {
    }
}
