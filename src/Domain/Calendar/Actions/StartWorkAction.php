<?php

namespace Domain\Calendar\Actions;

use SergiX44\Nutgram\Nutgram;

class StartWorkAction
{
    public function __invoke(Nutgram $bot)
    {
        $bot->sendMessage('work!!');
    }
}
