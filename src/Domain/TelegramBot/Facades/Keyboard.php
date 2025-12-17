<?php

namespace Domain\TelegramBot\Facades;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Illuminate\Support\Facades\Facade;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

/**
 * @method static void send(string $text, array $buttons)
 * @method static void remove()
 * @method static void back(string $text)
 * @method static ReplyKeyboardMarkup markup(array $buttons)
 */

class Keyboard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return KeyboardContract::class;
    }
}
