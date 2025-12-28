<?php

namespace Domain\TelegramBot\Contracts;

interface MessageContract
{
    public function text(string|array $text): self;
    public function delay(int $delay): self;
    public function replyKeyboard(array $keyboard): self;
    public function inlineKeyboard(array $keyboard): self;
    public function tryEditLast(bool $try = true): self;
    public function userId(int $userId): self;
    public function send(): void;

    public static function fake(): void;

    public function getLog(): array;
}
