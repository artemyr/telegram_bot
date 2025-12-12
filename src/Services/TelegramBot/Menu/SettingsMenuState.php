<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SettingsMenuState extends MenuState
{
    public function render(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Настройки',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'настройки 1', callback_data: 'settings_1'))
                ->addRow(InlineKeyboardButton::make(text: 'настройки 2', callback_data: 'settings_2'))
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            'settings_1' => $bot->sendMessage('1'),
            'settings_2' => $bot->sendMessage('2'),
            'back' => new MainMenuState(),
            default => null,
        };
    }
}
