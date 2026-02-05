<?php

namespace Domain\Schedule\Calendar\Jobs;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Jobs\NotificationJob;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class TimersRecoverJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        Timer::query()
            ->select(['id', 'title', 'startDate'])
            ->chunk(10, function (Collection $timers) {
                foreach ($timers as $timer) {

                    if ($timer->startDate->getTimestamp() < now()->getTimestamp()) {
                        continue;
                    }

                    dispatch(new NotificationJob(Timer::class, $timer->id, 'Таймер (восстановлен): ' . $timer->title, $timer->startDate))
                        ->delay($timer->startDate);
                }
            });
    }
}
