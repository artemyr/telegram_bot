<?php

namespace App\Console\Commands\Telegram\ButtonCommands;

use Domain\TelegramBot\Exceptions\PrintableException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use JsonException;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class RemoveWebhookCommand extends Command
{
    protected $signature = 'bot:t:hook:remove';
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
            schedule_bot()->sendMessage('Запрещено!');
            $this->fail("Can't use it on local");
        }

        if (!Gate::allows('remove_telegram_hook')) {
            schedule_bot()->sendMessage('Запрещено!');
            $this->fail("Forbidden");
        }

        schedule_bot()->sendMessage('Отключаю!');
        schedule_bot()->deleteWebhook();
        return self::SUCCESS;
    }
}
