<?php

namespace Domain\TelegramBot\Facades;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\UserStateDto;
use Illuminate\Support\Facades\Facade;

/**
 * @method static UserStateDto|null get(int $userId);
 * @method static void write(UserStateDto $user)
 * @method static UserStateDto make(int $userId, BotState $state, bool $keyboard = false)
 *
 * @method static void changeState(int $userId, BotState $state)
 * @method static void changeKeyboard(int $userId, bool $active)
 */
class UserState extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserStateContract::class;
    }
}
