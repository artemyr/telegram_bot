<?php

namespace Support\Contracts;

use Domain\Schedule\Tasks\Enum\TaskRepeatTypesEnum;
use Services\HumanDateParser\Collection\RecurrenceCollection;

interface HumanDateParserContract
{
    public function fromString(string $date, ?string $tz = null): HumanDateParserContract;

    public function getTimezone(): string;

    public function getStartString(): string;

    public function getType(): TaskRepeatTypesEnum;

    public function setType(TaskRepeatTypesEnum $value): HumanDateParserContract;

    public function isError(): bool;

    public function getErrorCode(): int;

    public function getCollection(): RecurrenceCollection;
}
