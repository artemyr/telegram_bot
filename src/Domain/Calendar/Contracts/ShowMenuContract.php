<?php

namespace Domain\Calendar\Contracts;

use SergiX44\Nutgram\Nutgram;

interface ShowMenuContract
{
    public function __invoke(Nutgram $bot);
}
