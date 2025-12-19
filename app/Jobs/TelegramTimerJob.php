<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TelegramTimerJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(
        protected int    $chatId,
        protected int    $userId,
        protected int    $timerId,
        protected string $class,
        protected string $method,
    )
    {
    }

    public function handle(): void
    {
        (new $this->class())->{$this->method}($this->chatId, $this->timerId);

        logger()->debug('Job executed. timer finished ' . $this->timerId);
    }

    public function uniqueId(): string
    {
        return md5(self::class . $this->chatId . $this->userId . $this->timerId . $this->class . $this->method);
    }
}
