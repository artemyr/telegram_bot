<?php

namespace App\Jobs;

use Domain\Calendar\Models\Timer;
use Domain\Tasks\Models\Task;
use Domain\TelegramBot\Models\Notifications;
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
        logger()->debug('Start job exec ' . self::class);

        $notifications = Notifications::query()
            ->with('notifiable')
            ->arrived()
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

                message()
                    ->text($text)
                    ->userId($task->telegram_user_id)
                    ->send();
            }

            if ($notifiable instanceof Timer) {
                $timer = $notifiable;

                message()
                    ->text($notification->message)
                    ->userId($timer->telegram_user_id)
                    ->send();
            }

            $notification->delete();
        }

        logger()->debug('Job executed. ' . self::class);
    }
}
