<?php

namespace App\Http\Controllers\Telegram\Schedule;

use App\Http\Controllers\Telegram\TelegramStateTrait;
use Domain\TelegramBot\Enum\LastMessageType;
use Domain\TelegramBot\MenuBotState;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Command\BotCommandScopeAllPrivateChats;

class StartController extends Command
{
    use TelegramStateTrait;

    protected string $command = 'start';
    protected ?string $description = 'Let\'s start a telegram bot';

    protected array $localizedDescriptions = [
        'ru' => 'Начать общение с ботом',
    ];

    public function scopes(): array
    {
        return [
            new BotCommandScopeAllPrivateChats,
        ];
    }

    public function handle(Nutgram $bot)
    {
        tuser()->changeLastMessageType(LastMessageType::USER_MESSAGE);

        try_to(function () {
            $userDto = tuser()->get();
            $state = $userDto->state ?? new MenuBotState();
            $state->render();
        }, function ($e) {
            $this->handleException($e);
        });
    }
}
