<?php

namespace Domain\Calendar\Actions;

use Domain\Calendar\Contracts\ShowMenuContract;
use Domain\Calendar\Enum\CalendarEnum;
use Domain\TelegramBot\Enum\MenuEnum;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class ShowMenuAction implements ShowMenuContract
{
    public function __invoke(Nutgram $bot)
    {
        $keyboard = ReplyKeyboardMarkup::make()
            ->addRow(KeyboardButton::make(text: MenuEnum::BACK->value));

        foreach (CalendarEnum::cases() as $case) {
            $keyboard->addRow(
                KeyboardButton::make(text: $case->value)
            );
        }

        $bot->sendMessage(
            text: "Раздел: Календарь\nВыберите что хотите сделать",
            reply_markup: $keyboard
        );
    }
}
