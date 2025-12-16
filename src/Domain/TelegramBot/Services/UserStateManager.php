<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\UserDto;
use Domain\TelegramBot\UserStateStore;

class UserStateManager implements UserStateContract
{
    public function load(int $userId): ?UserDto
    {
        return UserStateStore::get($userId);
    }

    public function write(UserDto $user): void
    {
        UserStateStore::set($user->userId, $user);
    }

    public function make(
        int      $userId,
        string   $path,
        BotState $state,
        bool     $keyboard = false,
        array    $actions = []
    ): UserDto
    {
        return new UserDto(
            $userId,
            $path,
            $state,
            $keyboard,
            $actions
        );
    }

    public function changePath(int $userId, string $path): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $path,
            $userDto->state,
            $userDto->keyboard,
            $userDto->actions,
        );

        $this->write($newUserDto);
    }

    public function changeState(int $userId, BotState $state): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $state,
            $userDto->keyboard,
            $userDto->actions,
        );

        $this->write($newUserDto);
    }

    public function changeKeyboard(int $userId, bool $active): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $active,
            $userDto->actions,
        );

        $this->write($newUserDto);
    }

    public function changeAction(int $userId, string $actionName, $value): void
    {
        $userDto = $this->load($userId);

        $actions = $userDto->actions;
        $actions[$actionName] = $value;

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $userDto->keyboard,
            $actions,
        );

        $this->write($newUserDto);
    }
}
