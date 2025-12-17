<?php

namespace Domain\TelegramBot\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

interface KeyboardContract
{
    public const BACK = '🔙 Назад';
    public function send(string $text, array $buttons): void;
    public function remove(): void;
    public function back(string $text): void;
    public function markup(array $buttons): ReplyKeyboardMarkup;
}
