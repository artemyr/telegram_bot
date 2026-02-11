<?php

namespace Domain\TelegramBot\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class SendMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $botName,
        protected int $userId,
        protected string $message,
        protected InlineKeyboardMarkup|ReplyKeyboardMarkup|null $keyboard = null,
    )
    {
    }

    public function handle(): void
    {
        init_bot(config("telegram_bot.bots.$this->botName.factory"));
        nutgram()->sendMessage(
            text: $this->message,
            chat_id: $this->userId,
            reply_markup: $this->keyboard,
        );
    }
}
