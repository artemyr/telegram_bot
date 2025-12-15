<?php

namespace Domain\Calendar\Actions;

use App\Jobs\WorkSession;

class WorkAction
{
    public function __invoke(): void
    {
        bot()->sendMessage('Через 45 минут отдых. Я напомню');
        dispatch(new WorkSession(bot()->chatId()))->delay(now()->addMinutes(1));
    }
}
