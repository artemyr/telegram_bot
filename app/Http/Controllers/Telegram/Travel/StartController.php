<?php

namespace App\Http\Controllers\Telegram\Travel;

use App\Http\Controllers\Telegram\TelegramStateTrait;
use Domain\TelegramBot\Enum\LastMessageType;
use Domain\TelegramBot\MenuBotState;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;

class StartController extends Command
{
    use TelegramStateTrait;

    protected string $command = 'start';
    protected ?string $description = 'Let\'s start a telegram bot';

    public function handle(Nutgram $bot)
    {
        tuserstate()->changeLastMessageType(LastMessageType::USER_MESSAGE);

        try_to(function () {
            $userDto = tuser();
            $state = $userDto->state ?? new MenuBotState();
            $state->render();
        }, function ($e) {
            $this->handleException($e);
        });
    }
}
