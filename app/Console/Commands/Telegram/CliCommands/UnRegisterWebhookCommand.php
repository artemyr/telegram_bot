<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\Schedule\Factory\ScheduleBotFactory;
use Domain\Travel\Factory\TravelBotFactory;
use Illuminate\Console\Command;

class UnRegisterWebhookCommand extends Command
{
    protected $signature = 't:hook:remove {bot_name? : Bot name}';
    protected $description = 'Удалить вебхуки для ботов';

    public function handle()
    {
        if (app()->isLocal()) {
            $this->fail("Can't use it on local");
        }

        $choice = $this->argument('bot_name');

        if (empty($choice)) {
            $choice = $this->choice('What bot need to UNregister hook?', [
                'schedule',
                'travel',
                'all',
            ], 'schedule');
        }

        switch ($choice) {
            case 'schedule':
                $this->info('schedule');
                init_bot(ScheduleBotFactory::class)->deleteWebhook();
                break;
            case 'travel':
                $this->info('travel');
                init_bot(TravelBotFactory::class)->deleteWebhook();
                break;
            case 'all':
                $this->info('travel and schedule');
                init_bot(ScheduleBotFactory::class)->deleteWebhook();
                init_bot(TravelBotFactory::class)->deleteWebhook();
                break;
            default:
                $this->fail("Unknown bot name");
        }

        $this->info('removed');

        return self::SUCCESS;
    }
}
