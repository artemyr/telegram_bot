<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\TelegramBot\Exceptions\PrintableException;
use Illuminate\Console\Command;

class RegisterWebhookCommand extends Command
{
    protected $signature = 't:hook:set {bot_name? : Bot name}';
    protected $description = 'Зарегистрировать вебхуки для ботов';

    /**
     * @throws PrintableException
     */
    public function handle()
    {
        if (app()->isLocal()) {
            $this->fail("Can't use it on local");
        }

        $choice = $this->argument('bot_name');

        if (empty($choice)) {
            $choice = $this->choice('What bot need to register hook?', [
                'schedule',
                'travel',
                'all',
            ], 'schedule');
        }

        switch ($choice) {
            case 'schedule':
                $this->info('schedule');
                bot('schedule')->setWebhook(config('app.url') . "/api/webhook/schedule", null, config('telegram_bot.serverip'));
                break;
            case 'travel':
                $this->info('travel');
                bot('travel')->setWebhook(config('app.url') . "/api/webhook/travel", null, config('telegram_bot.serverip'));
                break;
            case 'all':
                $this->info('travel and schedule');
                bot('schedule')->setWebhook(config('app.url') . "/api/webhook/schedule", null, config('telegram_bot.serverip'));
                bot('travel')->setWebhook(config('app.url') . "/api/webhook/travel", null, config('telegram_bot.serverip'));
                break;
            default:
                $this->fail("Unknown bot name");
        }

        return self::SUCCESS;
    }
}
