<?php

namespace App\Jobs;

use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WorkSession implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int $chatId,
        protected int $userId,
        protected string $message,
        protected ActionStateDto $action,
        protected string $unique,
    )
    {
    }

    public function handle(): void
    {
        bot()->sendMessage(
            text: $this->message,
            chat_id: $this->chatId,
        );

        UserState::changeAction($this->userId, new ActionStateDto(
            $this->action->class,
            true,
            $this->action->createDate,
            $this->action->startDate,
            $this->action->code,
            $this->action->title,
        ));
    }

    public function uniqueId(): string
    {
        return $this->unique;
    }
}
