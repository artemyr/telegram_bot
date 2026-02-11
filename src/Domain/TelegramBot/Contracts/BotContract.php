<?php

namespace Domain\TelegramBot\Contracts;

use SergiX44\Nutgram\Nutgram;

interface BotContract
{
    public function current(): Nutgram;
    public function username(): string;
    public function role(): string;
}
