<?php

namespace Domain\TelegramBot\Dto;

use Support\Traits\Makeable;

class ActionStateDto
{
    use Makeable;

    public function __construct(
        public readonly string $class,
        public readonly bool   $finished,
        public readonly string $createDate,
        public readonly string $startDate,
        public readonly string $code,
        public readonly string $title,
    )
    {
    }
}
