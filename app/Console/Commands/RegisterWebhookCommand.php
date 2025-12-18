<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegisterWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook:register';
    protected $description = 'Зарегистрировать вебхук для бота';

    public function handle()
    {
        $this->call('nutgram:hook:set', [
            'url' => config('app.url') . "/api/webhook"
        ]);
    }
}
