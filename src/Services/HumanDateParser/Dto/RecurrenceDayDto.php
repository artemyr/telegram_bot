<?php

namespace Services\HumanDateParser\Dto;

use Carbon\Carbon;

readonly class RecurrenceDayDto
{
    public function __construct(
        public Carbon $time,
    ) {
    }
}
