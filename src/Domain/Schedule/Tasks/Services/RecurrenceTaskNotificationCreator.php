<?php

namespace Domain\Schedule\Tasks\Services;

use App\Jobs\Telegram\Schedule\NotificationJob;
use Domain\Schedule\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Schedule\Tasks\Models\Task;
use Domain\Schedule\Tasks\Models\TaskRecurrence;

class RecurrenceTaskNotificationCreator implements RecurrenceTaskNotificationCreatorContract
{
    public function generateForRule(TaskRecurrence $rule, $from, $to): void
    {
        $dates = match ($rule->type) {
            'daily' => $this->dailyDates($rule, $from, $to),
            'weekly' => $this->weeklyDates($rule, $from, $to),
            'monthly' => $this->monthlyDates($rule, $from, $to),
            default => [],
        };

        foreach ($dates as $date) {
            dispatch(new NotificationJob(Task::class, $rule->task_id, null, $date))
                ->delay($date);
        }
    }

    protected function dailyDates(TaskRecurrence $rule, $from, $to): array
    {
        $dates = [];

        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            $dates[] = $date->copy()->setTimeFromTimeString($rule->time);
        }

        return $dates;
    }

    protected function weeklyDates(TaskRecurrence $rule, $from, $to): array
    {
        $dates = [];
        $days = $rule->days_of_week; // [1,3]

        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            if (in_array($date->dayOfWeekIso, $days)) {
                $dates[] = $date->copy()->setTimeFromTimeString($rule->time);
            }
        }

        return $dates;
    }

    protected function monthlyDates(TaskRecurrence $rule, $from, $to): array
    {
        $dates = [];
        $days = $rule->days_of_month;

        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            if (in_array($date->day, $days)) {
                $dates[] = $date->copy()->setTimeFromTimeString($rule->time);
            }
        }

        return $dates;
    }
}
