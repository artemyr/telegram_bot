<?php

namespace App\Telegram\States;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\MenuBotState;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CalendarState extends BotState
{
    public bool $silent = true;

    public function render(Nutgram $bot): void
    {
        $bot->editMessageText(
            text: now()->format('Y-m-d H:i:s'),
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'Назад', callback_data: troute('categories')))
        );
    }

    public function handle(Nutgram $bot): ?BotState
    {
        return match ($bot->callbackQuery()->data) {
            troute('categories') => new MenuBotState(),
            default => null,
        };
    }
}
