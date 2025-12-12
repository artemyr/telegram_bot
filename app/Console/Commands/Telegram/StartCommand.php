<?php

namespace App\Console\Commands\Telegram;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use Services\TelegramBot\Bot;

class StartCommand extends Command
{
    protected string $command = 'start';
    protected ?string $description = 'Let\'s start a telegram bot';

    public function handle(Nutgram $bot)
    {
        $menu = new Bot;
        $menu($bot);
    }
}
