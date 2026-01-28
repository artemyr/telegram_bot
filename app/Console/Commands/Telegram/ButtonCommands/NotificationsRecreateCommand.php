<?php

namespace App\Console\Commands\Telegram\ButtonCommands;

use App\Jobs\Telegram\Schedule\Tasks\Recurrence\GenerateUserTaskOccurrencesJob;
use Illuminate\Console\Command;

class NotificationsRecreateCommand extends Command
{
    protected $signature = 'bot:user:notifications:recreate';
    protected $description = 'Пересоздать все напоминания по задачам';

    public function handle()
    {
        $bot = nutgram();

        if (empty($bot->userId())) {
            $bot->sendMessage('Запрещено!');
            $this->fail('Не удалось определить пользователя');
        }

        dispatch(new GenerateUserTaskOccurrencesJob($bot->userId()));
        return self::SUCCESS;
    }
}
