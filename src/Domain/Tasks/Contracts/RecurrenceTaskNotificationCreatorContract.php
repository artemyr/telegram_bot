<?php

namespace Domain\Tasks\Contracts;

use Domain\Tasks\Models\TaskRecurrence;

interface RecurrenceTaskNotificationCreatorContract
{
    public function generateForRule(TaskRecurrence $rule, $from, $to): void;
}
