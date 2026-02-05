<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\TelegramBot\Exceptions\PrintableException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use JsonException;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use Throwable;

class RegisterWebhookCommand extends Command
{
    protected $signature = 't:hook:set {bot_name? : Bot name}';
    protected $description = 'Зарегистрировать вебхуки для ботов';

    /**
     * @return int
     * @throws GuzzleException
     * @throws JsonException
     * @throws TelegramException
     * @throws Throwable
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
                nutgram('schedule')->setWebhook(config('app.url') . "/api/webhook/schedule", null, config('telegram_bot.serverip'));
                break;
            case 'travel':
                $this->info('travel');
                nutgram('travel')->setWebhook(config('app.url') . "/api/webhook/travel", null, config('telegram_bot.serverip'));
                break;
            case 'all':
                $this->info('travel and schedule');
                nutgram('schedule')->setWebhook(config('app.url') . "/api/webhook/schedule", null, config('telegram_bot.serverip'));
                nutgram('travel')->setWebhook(config('app.url') . "/api/webhook/travel", null, config('telegram_bot.serverip'));
                break;
            default:
                $this->fail("Unknown bot name");
        }

        return self::SUCCESS;
    }
}
