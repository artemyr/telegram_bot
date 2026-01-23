<?php

namespace App\Console\Commands\Telegram\CliCommands;

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

        bot('schedule')->deleteWebhook();
        bot('travel')->deleteWebhook();

        $this->info('removed');

        return self::SUCCESS;
    }
}
