<?php

namespace App\Jobs\Tasks\Recurrence;

use Domain\Tasks\Contracts\RecurrenceTaskNotificationCreatorContract;
use Domain\Tasks\Models\TaskRecurrence;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateTaskOccurrencesJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(RecurrenceTaskNotificationCreatorContract $creator): void
    {
        $from = now()->startOfDay();
        $to = now()->addWeek()->endOfDay();

        TaskRecurrence::query()
            ->active()
            ->chunk(10, function ($rules) use ($from, $to, $creator) {
                foreach ($rules as $rule) {
                    $creator->generateForRule($rule, $from, $to);
                }
            });
    }
}
