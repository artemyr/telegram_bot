<?php

namespace Domain\Menu\Categories\Settings;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Services\TelegramBot\Menu\MenuState;

class SettingsInfoState extends MenuState
{
    public function __construct(protected string $text)
    {
    }

    public function render(Nutgram $bot): void
    {
        $bot->editMessageText(
            text: $this->text,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(text: 'Назад', callback_data: 'back'))
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
        return match ($bot->callbackQuery()->data) {
            'back' => new SettingsDetailState('Настройки'),
            default => null,
        };
    }
}
