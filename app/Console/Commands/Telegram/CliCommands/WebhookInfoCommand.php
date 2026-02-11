<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\Schedule\Factory\ScheduleBotFactory;
use Domain\Travel\Factory\TravelBotFactory;
use Illuminate\Console\Command;
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

        $this->showInfo(init_bot(ScheduleBotFactory::class)->getWebhookInfo());
        $this->showInfo(init_bot(TravelBotFactory::class)->getWebhookInfo());

        return self::SUCCESS;
    }

    protected function showInfo(WebhookInfo $botInfo): void
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
