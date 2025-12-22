<?php

namespace App\Jobs;

use App\Models\TaskRecurrence;
use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Models\Notifications;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class GenerateTaskOccurrencesJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    protected static $weekMap = [
        'mon' => Carbon::MONDAY,
        'tue' => Carbon::TUESDAY,
        'wed' => Carbon::WEDNESDAY,
        'thu' => Carbon::THURSDAY,
        'fri' => Carbon::FRIDAY,
        'sat' => Carbon::SATURDAY,
        'sun' => Carbon::SUNDAY,
    ];

    public function __construct()
    {
    }

    public function handle(): void
    {
        logger()->debug('Start job exec ' . self::class);

        TaskRecurrence::query()
            ->chunk(10, function ($recurrence) {
                $recurrence->each(function (TaskRecurrence $taskRecurrence) {
                    $this->generate($taskRecurrence);
                });
            });

        logger()->debug('Job executed. ' . self::class);
    }

    protected function generate(TaskRecurrence $recurrence): void
    {
        $rule = $recurrence->rule;
        $until = now()->addDays(14);

        if ($rule['frequency'] === 'weekly') {
            foreach ($rule['days'] as $day => $times) {
                foreach ($times as $time) {

                    $date = Carbon::now();
                    $dayConst = self::$weekMap[$day];

                    if ($date->dayOfWeek !== $dayConst) {
                        $date = $date->next($dayConst);
                    }

                    $date->setTimeFromTimeString($time);

                    if ($date <= $until) {
                        Notifications::firstOrCreate([
                            'notifiable_id' => $recurrence->task_id,
                            'notifiable_type' => Task::class,
                            'date' => $date,
                        ]);
                    }
                }
            }
        }
    }
}
