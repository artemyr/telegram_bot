<?php

namespace App\Jobs;

use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Facades\UserState;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TelegramActionJob implements ShouldQueue, ShouldBeUnique
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

        $action = new ActionStateDto(
            $this->action->class,
            true,
            $this->action->createDate,
            $this->action->startDate,
            $this->action->code,
            $this->action->title,
        );

        UserState::changeAction($this->userId, $action);

        logger()->debug('Job executed. action stage' . json_encode($action));
    }

    public function uniqueId(): string
    {
        return $this->unique;
    }
}
