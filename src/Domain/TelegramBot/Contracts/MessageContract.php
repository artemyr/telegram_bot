<?php

namespace Domain\TelegramBot\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

interface MessageContract
{
    public function send(int $userId, string $message, ?ReplyKeyboardMarkup $keyboard = null): void;
}
