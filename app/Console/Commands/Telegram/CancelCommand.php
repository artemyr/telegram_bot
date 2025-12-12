<?php

namespace App\Console\Commands\Telegram;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class CancelCommand extends Command
{
    protected string $command = 'cancel';
    protected ?string $description = 'Отмена';

    public function handle(Nutgram $bot)
    {
        $bot->sendMessage(
            text: 'Bye',
            reply_markup: ReplyKeyboardRemove::make(true),
        );
    }
}
