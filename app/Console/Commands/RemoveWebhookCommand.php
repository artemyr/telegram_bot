<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook:remove';
    protected $description = 'Удалить вебхук для бота';

    public function handle()
    {
        $this->call('nutgram:hook:remove');
    }
}
