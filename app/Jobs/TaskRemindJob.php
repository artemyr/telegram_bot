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
        logger()->debug('Start job exec ' . TaskRemindJob::class);

        TelegramUser::query()
            ->select(['id', 'telegram_id', 'chat_id', 'timezone'])
            ->with('tasks')
            ->chunk(10, function ($users) {

                logger()->debug('User batch ' . count($users));

                foreach ($users as $user) {
                    $table = TaskRepository::makeTable($user->tasks);

                    logger()->debug('User task batch ' . count($user->tasks));

                    $response = (string) $table;

                    if (empty($response)) {
                        logger()->debug('No messages for user');
                        continue;
                    }

                    logger()->debug('Sending '. $user->chat_id);

                    bot()->sendMessage(
                        text: "У вас в плане на сегодня: \n" . $response,
                        chat_id: $user->chat_id,
                    );
                }
            });

        logger()->debug('Job executed. ' . TaskRemindJob::class);
    }
}
