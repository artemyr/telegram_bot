<?php

namespace Domain\TelegramBot\Dto;

readonly class ActionStateDto
{
    public function __construct(
        public string $class,
        public bool   $finished,
        public string $createDate,
        public string $startDate,
        public string $code,
        public string $title,
    )
    {
    }
}
