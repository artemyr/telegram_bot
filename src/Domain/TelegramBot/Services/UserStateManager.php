<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\UserStateStore;

class UserStateManager implements UserStateContract
{
    public function load(int $userId): ?UserStateDto
    {
        return UserStateStore::get($userId);
    }

    public function write(UserStateDto $user): void
    {
        UserStateStore::set($user->userId, $user);
    }

    public function make(
        int      $userId,
        string   $path,
        BotState $state,
        bool     $keyboard = false,
        array    $actions = []
    ): UserStateDto
    {
        return new UserStateDto(
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

    public function changeAction(int $userId, ActionStateDto $action): void
    {
        $userDto = $this->load($userId);

        $oldActions = $userDto->actions;
        $oldActions[$action->code] = $action;

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $userDto->keyboard,
            $oldActions,
        );

        $this->write($newUserDto);
    }
}
