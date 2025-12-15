<?php

namespace Domain\Calendar\Actions;

use SergiX44\Nutgram\Nutgram;

class PiAction
{
    public function __invoke(Nutgram $bot)
    {
        $bot->sendMessage('pi pi');
    }
}
