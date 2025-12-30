<?php

namespace App\Jobs\Tasks\MorningNotify;

use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GlobalTaskRemindJob implements ShouldQueue, ShouldBeUnique
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
            ->chunk(10, function ($tusers) {
                foreach ($tusers as $tuser) {
                    $startDate = now();

                    if (!empty($tuser->timezone)) {
                        $startDate->setTimezone($tuser->timezone);
                    }

                    $startDate->setTime(9, 0);

                    dispatch(new UserTaskRemindJob($tuser->telegram_id))
                        ->delay($startDate);
                }
            });

        logger()->debug('Job executed. ' . self::class);
    }
}
