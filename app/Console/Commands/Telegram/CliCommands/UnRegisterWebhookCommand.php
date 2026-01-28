<?php

namespace App\Console\Commands\Telegram\CliCommands;

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
                nutgram('schedule')->deleteWebhook();
                break;
            case 'travel':
                $this->info('travel');
                nutgram('travel')->deleteWebhook();
                break;
            case 'all':
                $this->info('travel and schedule');
                nutgram('schedule')->deleteWebhook();
                nutgram('travel')->deleteWebhook();
                break;
            default:
                $this->fail("Unknown bot name");
        }

        $this->info('removed');

        return self::SUCCESS;
    }
}
