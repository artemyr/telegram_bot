<?php

namespace Domain\TelegramBot\Contracts;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Dto\UserStateDto;

interface UserStateContract
{
    public function get(): ?UserStateDto;
    public function write(UserStateDto $user): void;
    public function make(
        int $userId,
        BotState $state,
        bool $keyboard = false,
    ): UserStateDto;
    public function changeState(BotState $state): void;
    public function changeKeyboard(bool $active): void;
    public static function fake(): void;
}
