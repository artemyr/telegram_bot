<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveWebhookCommand extends Command
{
    protected $signature = 'telegram:hook:remove';
    protected $description = 'Удалить вебхук для бота';

    public function handle()
    {
        $this->call('nutgram:hook:remove');
    }
}
