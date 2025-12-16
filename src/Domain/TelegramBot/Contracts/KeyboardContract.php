<?php

namespace Domain\TelegramBot\Contracts;

interface KeyboardContract
{
    public function send(string $text, array $buttons): void;
    public function remove(): void;
    public function back(string $text): void;
}
