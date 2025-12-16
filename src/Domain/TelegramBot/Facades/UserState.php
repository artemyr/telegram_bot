<?php

namespace Domain\TelegramBot\Facades;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Dto\UserStateDto;
use Illuminate\Support\Facades\Facade;

/**
 * @method static UserStateDto|null load(int $userId);
 * @method static void write(UserStateDto $user)
 * @method static UserStateDto make(int $userId, string $path, BotState $state, string $timezone = '', bool $keyboard = false, array $actions = [])
 *
 * @method static void changePath(int $userId, string $path)
 * @method static void changeState(int $userId, BotState $state)
 * @method static void changeKeyboard(int $userId, bool $active)
 * @method static void changeTimezone(int $userId, string $timezone)
 * @method static void changeAction(int $userId, ActionStateDto $action)
 */
class UserState extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserStateContract::class;
    }
}
