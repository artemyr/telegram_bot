<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\Contracts\KeyboardContract;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class KeyboardManager implements KeyboardContract
{
    protected Nutgram $bot;

    public function __construct()
    {
        $this->bot = app(Nutgram::class);
    }

    public function remove(): void
    {
        $userDto = tuser();

        if (!$userDto) {
            $this->removeForce();
        }

        if ($userDto && $userDto->keyboard) {
            $this->removeForce();
        }
    }

    public function removeForce(): void
    {
        $this->bot->sendMessage(
            text: 'Removing keyboard...',
            reply_markup: ReplyKeyboardRemove::make(true),
        )?->delete();

        tuserstate()->changeKeyboard(false);
    }

    public function back(): array
    {
        return [
            KeyboardContract::BACK
        ];
    }

    public function markup(array $buttons):  ReplyKeyboardMarkup
    {
        $keyboard = ReplyKeyboardMarkup::make();

        foreach ($buttons as $button) {
            $keyboard->addRow(KeyboardButton::make(text: $button));
        }

        return $keyboard;
    }
}
