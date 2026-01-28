<?php

namespace App\Telegram\Contracts;

use Illuminate\Support\Carbon;

interface NotificationInstanceContract
{
    public function send(string $message, Carbon $date);
}
