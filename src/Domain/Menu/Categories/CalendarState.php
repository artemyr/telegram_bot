<?php

namespace Domain\Menu\Categories;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Services\TelegramBot\Menu\MenuState;

class CalendarState extends MenuState
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

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            troute('categories') => new MainMenuState(),
            default => null,
        };
    }
}
