<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SettingsDetailState extends MenuState
{
    public function __construct(public string $categoryName) {}

    public function render(Nutgram $bot): void
    {
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make(text: 'Описание', callback_data: 'info'))
            ->addRow(InlineKeyboardButton::make(text: 'Назад', callback_data: 'back'));

        $bot->editMessageText(text: "Настройка: {$this->categoryName}", reply_markup: $keyboard);
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        $data = $bot->callbackQuery()->data;

        return match ($data) {
            'info' => new SettingsInfoState($this->categoryName),
            'back' => new SettingsMenuState(),
        };
    }
}
