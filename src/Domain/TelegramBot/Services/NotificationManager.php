<?php

namespace Domain\TelegramBot\Services;

use App\Jobs\SendNotificationJob;
use App\Telegram\Contracts\NotificationInstanceContract;
use Illuminate\Support\Carbon;

class NotificationManager implements NotificationInstanceContract
{
    public function send(string $message, Carbon $date = null): void
    {
        $bot = nutgram();

        if (empty($date)) {
            dispatch(new SendNotificationJob(bot()->role(), $bot->userId(), $message));
        }

        dispatch(new SendNotificationJob(bot()->role(), $bot->userId(), $message))
            ->delay($date);
    }
}
