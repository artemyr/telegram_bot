<?php

namespace App\Telegram\States;

use Domain\Calendar\Contracts\ShowMenuContract;
use Domain\Calendar\Enum\CalendarEnum;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\MenuEnum;
use Domain\TelegramBot\MenuBotState;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class CalendarState extends BotState
{
    public bool $silent = true;

    public function render(Nutgram $bot): void
    {
        $action = app(ShowMenuContract::class);
        $action($bot);
    }

    public function handle(Nutgram $bot): ?BotState
    {
        if ($bot->message()->getText() === MenuEnum::BACK->value) {

            $bot->sendMessage(
                text: 'Removing keyboard...',
                reply_markup: ReplyKeyboardRemove::make(true),
            )?->delete();

            request()->merge([
                'path' => troute('categories')
            ]);

            return new MenuBotState();
        }

        foreach (CalendarEnum::cases() as $case) {
            if ($bot->message()->getText() === $case->value) {
                $bot->sendMessage("Вы отметили: " . $case->value);
            }
        }

        return new CalendarState();
    }
}
