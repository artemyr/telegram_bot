<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SettingsMenuState extends MenuState
{
    public function render(Nutgram $bot): void
    {
        $bot->editMessageText(
            text: 'Настройки',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'настройки 1', callback_data: 'settings_1'))
                ->addRow(InlineKeyboardButton::make(text: 'настройки 2', callback_data: 'settings_2'))
                ->addRow(InlineKeyboardButton::make(text: 'Назад', callback_data: 'back'))
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            'settings_1' => new SettingsDetailState('1'),
            'settings_2' => new SettingsDetailState('2'),
            'back' => new MainMenuState(),
            default => null,
        };
    }
}
