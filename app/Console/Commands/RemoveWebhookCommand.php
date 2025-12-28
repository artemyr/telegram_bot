<?php

namespace App\Console\Commands;

use Domain\TelegramBot\Exceptions\PrintableException;
use Illuminate\Console\Command;

class RemoveWebhookCommand extends Command
{
    protected $signature = 'telegram:hook:remove';
    protected $description = 'Удалить вебхук для бота';

    /**
     * @throws PrintableException
     */
    public function handle()
    {
        if (app()->isLocal()) {
            throw new PrintableException("Can't use it on local");
        }

        $this->call('nutgram:hook:remove');
    }
}
