<?php

namespace Domain\TelegramBot\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $botName,
        protected int $userId,
        protected string $message,
    ) {
    }

    public function handle(): void
    {
        init_bot(config("telegram_bot.bots.$this->botName.factory"));
        nutgram()->sendMessage(text: $this->message, chat_id: $this->userId);
    }
}
