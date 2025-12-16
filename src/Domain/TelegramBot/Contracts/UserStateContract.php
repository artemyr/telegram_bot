<?php

namespace Domain\TelegramBot\Contracts;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Dto\UserDto;

interface UserStateContract
{
    public function load(int $userId): ?UserDto;
    public function write(UserDto $user): void;
    public function make(
        int $userId,
        string $path,
        BotState $state,
        bool $keyboard = false,
        array $actions = []
    ): UserDto;

    public function changePath(int $userId, string $path): void;
    public function changeState(int $userId, BotState $state): void;
    public function changeKeyboard(int $userId, bool $active): void;
    public function changeAction(int $userId, string $actionName, $value): void;
}
