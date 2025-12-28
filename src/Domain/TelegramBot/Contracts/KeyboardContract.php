<?php

namespace Domain\TelegramBot\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

interface KeyboardContract
{
    public const BACK = '⏪ Назад';
    public function remove(): void;
    public function removeForce(): void;
    public function back(): array;
    public function markup(array $buttons): ReplyKeyboardMarkup;
}
