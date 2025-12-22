<?php

namespace Domain\TelegramBot\Contracts;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Dto\UserStateDto;

interface UserStateContract
{
    public function get(int $userId): ?UserStateDto;
    public function write(UserStateDto $user): void;
    public function make(
        int $userId,
        BotState $state,
        bool $keyboard = false,
    ): UserStateDto;
    public function changeState(int $userId, BotState $state): void;
    public function changeKeyboard(int $userId, bool $active): void;
}
