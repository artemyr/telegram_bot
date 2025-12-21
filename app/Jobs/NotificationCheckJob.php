<?php

namespace App\Jobs;

use App\Models\Notifications;
use Domain\Tasks\Models\Task;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class NotificationCheckJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $notifications = Notifications::query()
            ->where('date', '<=', now())
            ->get();

        foreach ($notifications as $notification) {
            $notifiable = $notification->notifiable;

            if ($notifiable instanceof Task) {
                $task = $notifiable;

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

                bot()->sendMessage(
                    text: implode("\n", $text),
                    chat_id: $task->telegram_user_id
                );
            }

            $notification->delete();
        }

        logger()->debug('Job executed. ' . self::class);
    }

    public function uniqueId(): string
    {
        return md5(self::class);
    }
}
