<?php

namespace App\Console\Commands\Telegram\ButtonCommands;

use App\Jobs\Tasks\Recurrence\GenerateUserTaskOccurrencesJob;
use Illuminate\Console\Command;

class NotificationsRecreateCommand extends Command
{
    protected $signature = 'bot:user:notifications:recreate';
    protected $description = 'Пересоздать все напоминания по задачам';

    public function handle()
    {
        $tuserId = schedule_bot()->userId();

        if (empty($tuserId)) {
            schedule_bot()->sendMessage('Запрещено!');
            $this->fail('Не удалось определить пользователя');
        }

        dispatch(new GenerateUserTaskOccurrencesJob(schedule_bot()->userId()));
        return self::SUCCESS;
    }
}
