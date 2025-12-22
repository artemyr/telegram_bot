<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Facades\UserState;
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
        $userDto = UserState::get(bot()->userId());

        if (!$userDto) {
            $this->bot->sendMessage(
                text: 'Removing keyboard...',
                reply_markup: ReplyKeyboardRemove::make(true),
            )?->delete();
        }

        if ($userDto && $userDto->keyboard) {
            $this->bot->sendMessage(
                text: 'Removing keyboard...',
                reply_markup: ReplyKeyboardRemove::make(true),
            )?->delete();

            UserState::changeKeyboard($this->bot->userId(), false);
        }
    }

    public function back(): ReplyKeyboardMarkup
    {
        $keyboard = [
            KeyboardContract::BACK
        ];

        return $this->markup($keyboard);
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
