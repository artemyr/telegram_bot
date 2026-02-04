<?php

namespace Domain\Schedule\Jobs\Tasks\Recurrence;

use Domain\Schedule\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Schedule\Tasks\Models\Task;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateUserTaskOccurrencesJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int $tuserId
    )
    {
    }

    public function handle(RecurrenceTaskNotificationCreatorContract $creator): void
    {
        $from = now()->startOfDay();
        $to = now()->addWeek()->endOfDay();

        Task::query()
            ->where('telegram_user_id', $this->tuserId)
            ->with('taskRecurrences')
            ->repeat()
            ->chunk(10, function ($tasks) use ($from, $to, $creator) {
                foreach ($tasks as $task) {
                    $recurrences = $task->taskRecurrences;
                    foreach ($recurrences as $recurrence) {

                        if (!$recurrence->is_active) {
                            continue;
                        }

                        $creator->generateForRule($recurrence, $from, $to);
                    }
                }
            });
    }
}
