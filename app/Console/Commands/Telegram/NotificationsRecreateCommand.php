<?php

namespace App\Console\Commands\Telegram;

use App\Jobs\Tasks\Recurrence\GenerateUserTaskOccurrencesJob;
use Illuminate\Console\Command;

class NotificationsRecreateCommand extends Command
{
    protected $signature = 'user:notifications:recreate';
    protected $description = 'Пересоздать все напоминания по задачам';

    public function handle()
    {
       dispatch(new GenerateUserTaskOccurrencesJob(bot()->userId()));
    }
}
