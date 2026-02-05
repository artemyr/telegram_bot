<?php

namespace Domain\Schedule\Jobs;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Tasks\Models\Task;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class NotificationJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * @param class-string $model
     * @param int $id
     * @param string|null $message
     * @param string|null $salt
     */
    public function __construct(
        protected string $model,
        protected int $id,
        protected ?string $message = null,
        protected ?string $salt = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        init_bot('schedule');

        $notifiable = $this->model;

        match ($notifiable) {
            Task::class => $this->task($notifiable),
            Timer::class => $this->timer($notifiable),
            default => throw new Exception('Job exec failed ' . self::class)
        };
    }

    public function uniqueId(): string
    {
        return md5(self::class . $this->model . $this->id . $this->message . $this->salt);
    }

    /** @var class-string $notifiable */
    protected function task(string $notifiable)
    {
        $task = $notifiable::query()
            ->where('id', $this->id)
            ->first();

        if (empty($task)) {
            return;
        }

        /** @var Carbon $deadline */
        $deadline = $task->deadline;

        $text = ["Напоминание о задаче:"];

        if ($deadline) {
            $tuser = $task->telegramUser;
            $now = now($tuser->timezone);
            $userDeadline = $deadline
                ->setTimezone($tuser->timezone);
            $text[] = "Осталось " . $now->diffForHumans($userDeadline);
        }

        $text[] = "\"" . $task->title . "\"";

        if ($deadline) {
            $text[] = $userDeadline
                ->format('d.m.Y H:i');
        }

        message()
            ->text($text)
            ->userId($task->telegram_user_id)
            ->send();
    }

    /** @var class-string $notifiable */
    protected function timer(string $notifiable)
    {
        $timer = $notifiable::query()
            ->where('id', $this->id)
            ->first();

        if (empty($timer)) {
            return;
        }

        message()
            ->text($this->message)
            ->userId($timer->telegram_user_id)
            ->send();
    }
}
