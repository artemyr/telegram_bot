<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\TelegramBot\Exceptions\PrintableException;
use Illuminate\Console\Command;

class RegisterWebhookCommand extends Command
{
    protected $signature = 't:hook:set';
    protected $description = 'Зарегистрировать вебхук для бота';

    /**
     * @throws PrintableException
     */
    public function handle()
    {
        if (app()->isLocal()) {
            $this->fail("Can't use it on local");
        }

        $this->call('nutgram:hook:set', [
            'url' => config('app.url') . "/api/webhook",
            '--ip' => config('telegram_bot.serverip')
        ]);

        return self::SUCCESS;
    }
}
