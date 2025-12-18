<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class RegisterTelegramCommandsCommand extends Command
{
    protected $signature = 'telegram:commands:register';
    protected $description = 'Зарегистрировать команды бота';

    public function handle(Nutgram $bot)
    {
        $bot->registerMyCommands();
    }
}
