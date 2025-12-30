<?php

namespace App\Jobs\Tasks\Recurrence;

use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Models\Notifications;
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

    public function handle(): void
    {
        logger()->debug('Start job exec ' . self::class);

        $from = now()->startOfDay();
        $to = now()->addWeek()->endOfDay();

        $task = Task::query()
            ->where('id', $this->taskId)
            ->with('taskRecurrences')
            ->first();

        if (!empty($task->taskRecurrences)) {
            foreach ($task->taskRecurrences as $rule) {
                $this->generateForRule($rule, $from, $to);
            }
        }

        logger()->debug('Job executed. ' . self::class);
    }

    protected function generateForRule($rule, $from, $to): void
    {
        $dates = match ($rule->type) {
            'daily' => $this->dailyDates($rule, $from, $to),
            'weekly' => $this->weeklyDates($rule, $from, $to),
            'monthly' => $this->monthlyDates($rule, $from, $to),
            default => [],
        };

        foreach ($dates as $date) {
            Notifications::firstOrCreate([
                'date' => $date,
                'notifiable_id' => $rule->task_id,
                'notifiable_type' => Task::class,
            ]);
        }
    }

    protected function dailyDates($rule, $from, $to): array
    {
        $dates = [];

        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            $dates[] = $date->copy()->setTimeFromTimeString($rule->time);
        }

        return $dates;
    }

    protected function weeklyDates($rule, $from, $to): array
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

    protected function monthlyDates($rule, $from, $to): array
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
