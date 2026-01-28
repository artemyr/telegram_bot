<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class SendMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $userId,
        protected string $message,
        protected ?ReplyKeyboardMarkup $keyboard = null,
    )
    {
    }

    public function handle(): void
    {
        nutgram('schedule')->sendMessage(
            text: $this->message,
            chat_id: $this->userId,
            reply_markup: $this->keyboard,
        );
    }
}
