<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class MainMenuState extends MenuState
{
    public function render(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Главное меню',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'Категории', callback_data: 'categories'))
                ->addRow(InlineKeyboardButton::make(text: 'Настройки', callback_data: 'settings'))
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            'categories' => new CategoriesMenu(),
            'settings' => new SettingsMenuState(),
            default => null,
        };
    }
}
