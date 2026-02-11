<?php

namespace Domain\TelegramBot\Contracts;

use Illuminate\Support\Carbon;

interface NotificationInstanceContract
{
    public function send(string $message, Carbon $date = null): void;
}
