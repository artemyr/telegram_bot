<?php

namespace Domain\Menu\Categories;

use Domain\Menu\Categories\Categories\CategoriesMenuState;
use Domain\Menu\Categories\Settings\SettingsMenuState;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Services\TelegramBot\Menu\MenuState;

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
            'categories' => new CategoriesMenuState(),
            'settings' => new SettingsMenuState(),
            default => null,
        };
    }
}
