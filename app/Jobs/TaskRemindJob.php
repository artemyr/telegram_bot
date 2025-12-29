<?php

namespace App\Jobs;

use Domain\Tasks\Models\Task;
use Domain\Tasks\Presentations\TaskPresentation;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;

class TaskRemindJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        logger()->debug('Start job exec ' . self::class);

        TelegramUser::query()
            ->select(['id', 'telegram_id', 'timezone'])
            ->with('tasks')
            ->chunk(10, function ($users) {
                foreach ($users as $user) {
                    if ($this->isTime($user)) {
                        $this->recalculateTaskPriority($user);
                        $this->notify($user);
                    }
                }
            });

        logger()->debug('Job executed. ' . self::class);
    }

    protected function isTime(TelegramUser $user): bool
    {
        $now = now();
        $start = now();
        $end = now();

        if (!empty($user->timezone)) {
            $now->setTimezone($user->timezone);
            $start->setTimezone($user->timezone);
            $end->setTimezone($user->timezone);
        }

        $start = $start->setTime(9, 00);
        $end = $end->setTime(9, 05);


        if (!$now->between($start, $end)) {
            return false;
        }

        return true;
    }

    private function recalculateTaskPriority(TelegramUser $user): void
    {
        $now = now();
        if (!empty($user->timezone)) {
            $now->setTimezone($user->timezone);
        }

        $tasks = Task::query()
            ->select(['id', 'priority'])
            ->where('telegram_user_id', $user->telegram_id)
            ->single()
            ->whereNull('deadline')
            ->get();

        foreach ($tasks as $task) {
            if ($task->priority < 100) {
                $task->increment('priority');
            }
        }
    }

    private function notify(TelegramUser $user): void
    {
        $now = now();
        $todayEnd = now();

        if (!empty($user->timezone)) {
            $now->setTimezone($user->timezone);
            $todayEnd->setTimezone($user->timezone);
        }

        $todayEnd = $todayEnd->endOfDay();

        $tasks = Task::query()
            ->where('telegram_user_id', $user->telegram_id)
            ->where(function (Builder $q) use ($todayEnd) {
                $q->whereNull('deadline')
                    ->orWhere('deadline', '<=', $todayEnd);
            })
            ->get();

        $response = (string)(new TaskPresentation($tasks, $user->timezone));

        if (empty($response)) {
            return;
        }

        logger()->debug('Sending ' . $user->telegram_id);

        message()
            ->text("У вас в плане на сегодня: \n" . $response)
            ->userId($user->telegram_id)
            ->send();
    }
}
