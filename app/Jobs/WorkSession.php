<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WorkSession implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $chatId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        bot()->sendMessage(
            text: 'Пора отдыхать',
            chat_id: $this->chatId
        );
    }

    public function uniqueId() //?
    {
        return 'work-session';
    }
}
