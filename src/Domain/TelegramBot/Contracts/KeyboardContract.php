<?php

namespace Domain\TelegramBot\Contracts;

use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

interface KeyboardContract
{
    public function remove(): void;
    public function removeForce(): void;
    public function back(): array;
    public function prev(): array;
    public function next(): array;
    public function pagination(): array;
    public function markup(array $buttons): ReplyKeyboardMarkup;
}
