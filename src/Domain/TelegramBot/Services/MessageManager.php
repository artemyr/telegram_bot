<?php

namespace Domain\TelegramBot\Services;

use App\Jobs\SendMessageImmediatelyJob;
use Domain\TelegramBot\Contracts\MessageContract;
use RuntimeException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class MessageManager implements MessageContract
{
    protected string $driver;

    public function __construct()
    {
        $this->driver = config('telegram_bot.messages.driver');
    }

    public function send(int $userId, string $message, ?ReplyKeyboardMarkup $keyboard = null): void
    {
        if ($this->driver === 'jobs') {
            dispatch(
                new SendMessageImmediatelyJob(
                    $userId,
                    $message,
                    $keyboard
                )
            );
            return;
        }

        if ($this->driver === 'runtime') {
            bot()->sendMessage(
                text: $message,
                chat_id: $userId,
                reply_markup: $keyboard,
            );
            return;
        }

        throw new RuntimeException('Driver not supported');
    }
}
