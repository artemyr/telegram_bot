<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\Contracts\KeyboardContract;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class KeyboardManager implements KeyboardContract
{
    protected mixed $bot;

    public function __construct()
    {
        $this->bot = app(Nutgram::class);
    }

    public function send(string $text, array $buttons): void
    {
        $keyboard = ReplyKeyboardMarkup::make();

        foreach ($buttons as $button) {
            $keyboard->addRow(KeyboardButton::make(text: $button));
        }

        $this->bot->sendMessage(
            text: $text,
            reply_markup: $keyboard
        );
    }

    public function remove(): void
    {
        $this->bot->sendMessage(
            text: 'Removing keyboard...',
            reply_markup: ReplyKeyboardRemove::make(true),
        )?->delete();
    }
}
