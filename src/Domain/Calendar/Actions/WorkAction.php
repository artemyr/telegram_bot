<?php

namespace Domain\Calendar\Actions;

use SergiX44\Nutgram\Nutgram;

class WorkAction
{
    public function __invoke(Nutgram $bot)
    {
        $bot->sendMessage('start session');
    }
}
