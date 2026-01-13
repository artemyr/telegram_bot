<?php

namespace App\Jobs\Tasks\MorningNotify;

use Domain\Tasks\Models\Task;
use Domain\Tasks\Presentations\TaskPresentation;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;

class UserTaskRemindJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int $userId
    )
    {
    }

    public function handle(): void
    {
        $tuser = TelegramUser::query()
            ->select(['id', 'telegram_id', 'timezone'])
            ->where('telegram_id', $this->userId)
            ->with('tasks')
            ->first();

        $this->recalculateTaskPriority($tuser);
        $this->notify($tuser);
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

        message()
            ->text("У вас в плане на сегодня: \n" . $response)
            ->userId($user->telegram_id)
            ->send();
    }

    public function uniqueId(): string
    {
        return self::class .'_'. $this->userId;
    }
}
