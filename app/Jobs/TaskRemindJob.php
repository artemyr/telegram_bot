<?php

namespace App\Jobs;

use Domain\Tasks\Repository\TaskRepository;
use Domain\TelegramBot\Models\TelegramUser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
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
            ->select(['id', 'telegram_id', 'chat_id', 'timezone'])
            ->with('tasks')
            ->chunk(10, function ($users) {
                logger()->debug('User batch ' . count($users));

                foreach ($users as $user) {
                    logger()->debug('Start finding user task ' . $user->telegram_id);

                    $now = now();
                    $start = now();
                    $end = now();

                    logger()->debug(sprintf('before timezone now %s start %s end %s', $now, $start, $end));

                    if (!empty($user->timezone)) {
                        logger()->debug('User timezone is ' . $user->timezone);
                        $now->setTimezone($user->timezone);
                        $start->setTimezone($user->timezone);
                        $end->setTimezone($user->timezone);
                    }

                    $start = $start->setTime(9, 00);
                    $end = $end->setTime(9, 05);

                    logger()->debug(sprintf('after timezone now %s start %s end %s', $now, $start, $end));

                    if (!$now->between($start, $end)) {
                        logger()->debug('Not time yet');
                        continue;
                    }

                    $table = TaskRepository::makeTable($user->tasks, $user);

                    logger()->debug('User task batch ' . count($user->tasks));

                    $response = (string)$table;

                    if (empty($response)) {
                        logger()->debug('No messages for user' . $user->telegram_id);
                        continue;
                    }

                    logger()->debug('Sending ' . $user->chat_id);

                    bot()->sendMessage(
                        text: "У вас в плане на сегодня: \n" . $response,
                        chat_id: $user->chat_id,
                    );
                }
            });

        logger()->debug('Job executed. ' . self::class);
    }
}
