<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CategoriesMenuState extends MenuState
{
    public function render(Nutgram $bot): void
    {
        $bot->editMessageText(
            text: 'Категории',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'категория 1', callback_data: 'categories_1'))
                ->addRow(InlineKeyboardButton::make(text: 'категория 2', callback_data: 'categories_2'))
                ->addRow(InlineKeyboardButton::make(text: 'Назад', callback_data: 'back'))
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            'categories_1' => new CategoryDetailState('категория 1'),
            'categories_2' => new CategoryDetailState('категория 2'),
            'back' => new MainMenuState(),
            default => null,
        };
    }
}
