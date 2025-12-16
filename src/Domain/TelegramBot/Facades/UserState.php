<?php

namespace Domain\TelegramBot\Facades;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\UserDto;
use Illuminate\Support\Facades\Facade;

/**
 * @method static UserDto|null load(int $userId);
 * @method static void write(UserDto $user)
 * @method static UserDto make(int $userId, string $path, BotState $state, bool $keyboard = false, array $actions = [])
 *
 * @method static void changePath(int $userId, string $path)
 * @method static void changeState(int $userId, BotState $state)
 * @method static void changeKeyboard(int $userId, bool $active)
 * @method static void changeAction(int $userId, string $actionName, $value)
 */
class UserState extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserStateContract::class;
    }
}
