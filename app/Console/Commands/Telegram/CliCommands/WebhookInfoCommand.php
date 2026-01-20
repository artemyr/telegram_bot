<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Common\WebhookInfo;

class WebhookInfoCommand extends Command
{
    protected $signature = 't:hook:info';
    protected $description = 'Информация о вебхуках';

    public function handle()
    {
        if (app()->isLocal()) {
            $this->fail("Can't use it on local");
        }

        $botInfo = schedule_bot()->getWebhookInfo();
        $this->showInfo('schedule', $botInfo);

        $botInfo = travel_bot()->getWebhookInfo();
        $this->showInfo('travel', $botInfo);

        return self::SUCCESS;
    }

    protected function showInfo(string $botName, WebhookInfo $botInfo): void
    {
        $name = $botInfo->getBot()?->getMyName()?->name;
        $this->alert("$name bot info");
        $this->info('url ' . $botInfo->url);
        $this->info('last_error_message ' . $botInfo->last_error_message);
        $this->info('last_error_date: ' . $botInfo->last_error_date);
        $this->info('ip_address ' . $botInfo->ip_address);
        $this->info('pending_update_count ' . $botInfo->pending_update_count);
        $this->info('last_synchronization_error_date ' . $botInfo->last_synchronization_error_date);
        $this->newLine(2);
    }
}
