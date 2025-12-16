<?php

namespace Domain\TelegramBot\Dto;

use Domain\TelegramBot\BotState;
use Support\Traits\Makeable;

class UserStateDto
{
    use Makeable;

    public function __construct(
        public readonly int      $userId,
        public readonly string   $path,
        public readonly BotState $state,
        public readonly bool     $keyboard,
        /** @param $actions ActionStateDto[] */
        public readonly array    $actions,
    )
    {
    }
}
