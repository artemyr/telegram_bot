<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Enum\KeyboardEnum;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class KeyboardManager implements KeyboardContract
{
    public function remove(): void
    {
        $userDto = tuser()->get();

        if (!$userDto) {
            $this->removeForce();
        }

        if ($userDto && $userDto->keyboard) {
            $this->removeForce();
        }
    }

    public function removeForce(): void
    {
        nutgram()->sendMessage(
            text: 'Removing keyboard...',
            reply_markup: ReplyKeyboardRemove::make(true),
        )?->delete();

        tuser()->changeKeyboard(false);
    }

    public function back(): array
    {
        return [
            KeyboardEnum::BACK->value => KeyboardEnum::BACK->label()
        ];
    }

    public function prev(): array
    {
        return [
            KeyboardEnum::PREV->value => KeyboardEnum::PREV->label()
        ];
    }

    public function next(): array
    {
        return [
            KeyboardEnum::NEXT->value => KeyboardEnum::NEXT->label()
        ];
    }

    public function pagination(): array
    {
        return [
            KeyboardEnum::BACK->value => KeyboardEnum::BACK->label(),
            [
                KeyboardEnum::PREV->value => KeyboardEnum::PREV->label(),
                KeyboardEnum::NEXT->value => KeyboardEnum::NEXT->label(),
            ]
        ];
    }

    public function markup(array $buttons): ReplyKeyboardMarkup
    {
        $keyboard = ReplyKeyboardMarkup::make();

        foreach ($buttons as $button) {
            $keyboard->addRow(KeyboardButton::make(text: $button));
        }

        return $keyboard;
    }
}
