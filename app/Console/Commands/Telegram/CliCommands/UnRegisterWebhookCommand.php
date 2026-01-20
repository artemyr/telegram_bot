<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\TelegramBot\Exceptions\PrintableException;
use Illuminate\Console\Command;

class UnRegisterWebhookCommand extends Command
{
    protected $signature = 't:hook:remove';
    protected $description = 'Удалить вебхуки для ботов';

    public function handle()
    {
        if (app()->isLocal()) {
            $this->fail("Can't use it on local");
        }

        schedule_bot()->deleteWebhook();
        travel_bot()->deleteWebhook();

        $this->info('removed');

        return self::SUCCESS;
    }
}
