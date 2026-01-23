<?php

namespace Domain\Schedule\Tasks\Contracts;

use Domain\Schedule\Tasks\Models\TaskRecurrence;

interface RecurrenceTaskNotificationCreatorContract
{
    public function generateForRule(TaskRecurrence $rule, $from, $to): void;
}
