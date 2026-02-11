<?php

namespace App\Console\Commands\Telegram\CliCommands;

use Domain\Schedule\Factory\ScheduleBotFactory;
use Domain\Travel\Factory\TravelBotFactory;
use Illuminate\Console\Command;
use Throwable;

class RegisterCommandsCommand extends Command
{
    protected $signature = 't:commands:set {bot_name? : Bot name}';
    protected $description = 'Зарегистрировать команды для ботов';

    /**
     * @return int
     * @throws Throwable
     */
    public function handle()
    {
        $choice = $this->argument('bot_name');

        if (empty($choice)) {
            $choice = $this->choice('What bot need to register commands?', [
                'schedule',
                'travel',
                'all',
            ], 'schedule');
        }

        switch ($choice) {
            case 'schedule':
                $this->info('schedule');
                init_bot(ScheduleBotFactory::class)->registerMyCommands();
                break;
            case 'travel':
                $this->info('travel');
                init_bot(TravelBotFactory::class, true)->registerMyCommands();
                break;
            case 'all':
                $this->info('travel and schedule');
                init_bot(ScheduleBotFactory::class)->registerMyCommands();
                init_bot(TravelBotFactory::class)->registerMyCommands();
                break;
            default:
                $this->fail("Unknown bot name");
        }

        return self::SUCCESS;
    }
}
