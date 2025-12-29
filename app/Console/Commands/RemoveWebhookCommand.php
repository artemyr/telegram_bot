<?php

namespace App\Console\Commands;

use Domain\TelegramBot\Exceptions\PrintableException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use JsonException;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class RemoveWebhookCommand extends Command
{
    protected $signature = 't:hook:remove';
    protected $description = 'Удалить вебхук для бота';

    /**
     * @throws PrintableException
     * @throws GuzzleException
     * @throws JsonException
     * @throws TelegramException
     */
    public function handle()
    {
        if (app()->isLocal()) {
            throw new PrintableException("Can't use it on local");
        }

        bot()->deleteWebhook();
    }
}
