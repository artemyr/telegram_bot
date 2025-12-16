<?php

namespace Domain\TelegramBot\Contracts;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Dto\UserStateDto;

interface UserStateContract
{
    public function get(int $userId): ?UserStateDto;
    public function load(int $userId): UserStateDto;
    public function write(UserStateDto $user): void;
    public function make(
        int $userId,
        string $path,
        BotState $state,
        string $timezone = '',
        bool $keyboard = false,
        bool $callbackQuery = false,
        array $actions = []
    ): UserStateDto;

    public function changePath(int $userId, string $path): void;
    public function changeState(int $userId, BotState $state): void;
    public function changeKeyboard(int $userId, bool $active): void;
    public function changeTimezone(int $userId, string $timezone): void;
    public function changeCallbackQuery(int $userId, bool $active): void;
    public function changeAction(int $userId, ActionStateDto $action): void;
}
