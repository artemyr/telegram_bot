<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\UserStateStore;

class UserStateManager implements UserStateContract
{
    public function get(int $userId): ?UserStateDto
    {
        return UserStateStore::get($userId);
    }

    public function load(int $userId): UserStateDto
    {
        $userDto = UserStateStore::get($userId);

        if (!$userDto) {
            $userDto = $this->make($userId, troute('home'), new MenuBotState());
            $this->write($userDto);
        }

        return $userDto;
    }

    public function write(UserStateDto $user): void
    {
        UserStateStore::set($user->userId, $user);

        logger()->debug('Write user state: ' . json_encode($user));
    }

    public function make(
        int      $userId,
        string   $path,
        BotState $state,
        string   $timezone = '',
        bool     $keyboard = false,
        bool     $callbackQuery = false,
        array    $actions = [],
    ): UserStateDto
    {
        return new UserStateDto(
            $userId,
            $path,
            $state,
            $timezone,
            $keyboard,
            $callbackQuery,
            $actions,
        );
    }

    public function changePath(int $userId, string $path): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $path,
            $userDto->state,
            $userDto->timezone,
            $userDto->keyboard,
            $userDto->callbackQuery,
            $userDto->actions,
        );

        $this->write($newUserDto);

        logger()->debug("User $userId request to: " . $path);
    }

    public function changeState(int $userId, BotState $state): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $state,
            $userDto->timezone,
            $userDto->keyboard,
            $userDto->callbackQuery,
            $userDto->actions,
        );

        $this->write($newUserDto);

        logger()->debug("Change user $userId stage to: " . get_class($state));
    }

    public function changeKeyboard(int $userId, bool $active): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $userDto->timezone,
            $active,
            $userDto->callbackQuery,
            $userDto->actions,
        );

        $this->write($newUserDto);

        logger()->debug("Change user $userId keyboard to: $active");
    }

    public function changeCallbackQuery(int $userId, bool $active): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $userDto->timezone,
            $userDto->keyboard,
            $active,
            $userDto->actions,
        );

        $this->write($newUserDto);

        logger()->debug("Change user $userId keyboard to: $active");
    }

    public function changeTimezone(int $userId, string $timezone): void
    {
        $userDto = $this->load($userId);

        $newUserDto = $this->make(
            $userDto->userId,
            $userDto->path,
            $userDto->state,
            $timezone,
            $userDto->keyboard,
            $userDto->callbackQuery,
            $userDto->actions,
        );

        $this->write($newUserDto);

        logger()->debug("Change user $userId timezone to: $timezone");
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
            $userDto->timezone,
            $userDto->keyboard,
            $userDto->callbackQuery,
            $oldActions,
        );

        $this->write($newUserDto);

        logger()->debug("Change user $userId action state: " . json_encode($newUserDto));
    }
}
