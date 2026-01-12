<?php

namespace App\Jobs;

use Domain\Calendar\Models\Timer;
use Domain\Tasks\Models\Task;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class NotificationJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

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
        logger()->debug('Start job exec ' . self::class);

        /** @var Model $notifiable */
        $notifiable = $this->model;

        switch ($notifiable) {
            case Task::class:
                $task = $notifiable::query()
                    ->where('id', $this->id)
                    ->first();

                if (empty($task)) {
                    logger()->debug('Job executed. Task not found ' . $this->id . self::class);
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
                break;

            case Timer::class:
                $timer = $notifiable::query()
                    ->where('id', $this->id)
                    ->first();

                if (empty($timer)) {
                    logger()->debug('Job executed. Timer not found ' . $this->id . self::class);
                    return;
                }

                logger()->debug('send message' . $this->id . ' ' . $timer->telegram_user_id . ' ' . $this->message);
                message()
                    ->text($this->message)
                    ->userId($timer->telegram_user_id)
                    ->send();
                break;

            default:
                throw new Exception('Job exec failed ' . self::class);
        }

        logger()->debug('Job executed. ' . self::class);
    }

    public function uniqueId(): string
    {
        return md5(self::class . $this->model . $this->id . $this->message . $this->salt);
    }
}
