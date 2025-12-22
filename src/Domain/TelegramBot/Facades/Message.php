<?php

namespace Domain\TelegramBot\Facades;

use Domain\TelegramBot\Contracts\MessageContract;
use Illuminate\Support\Facades\Facade;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

/**
 * @method static void send(int $userId, string $message, ReplyKeyboardMarkup $keyboard = null)
 */

class Message extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MessageContract::class;
    }
}
