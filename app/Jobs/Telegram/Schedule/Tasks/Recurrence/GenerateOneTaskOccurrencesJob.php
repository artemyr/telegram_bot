<?php

namespace App\Jobs\Telegram\Schedule\Tasks\Recurrence;

use Domain\Schedule\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Schedule\Tasks\Models\Task;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateOneTaskOccurrencesJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int $taskId
    )
    {
    }

    public function handle(RecurrenceTaskNotificationCreatorContract $creator): void
    {
        $from = now()->startOfDay();
        $to = now()->addWeek()->endOfDay();

        $task = Task::query()
            ->where('id', $this->taskId)
            ->with('taskRecurrences')
            ->first();

        if (!empty($task->taskRecurrences)) {
            foreach ($task->taskRecurrences as $rule) {
                $creator->generateForRule($rule, $from, $to);
            }
        }
    }
}
