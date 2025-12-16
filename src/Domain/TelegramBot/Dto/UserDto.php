<?php

namespace Domain\TelegramBot\Dto;

use Domain\TelegramBot\BotState;
use Support\Traits\Makeable;

class UserDto
{
    use Makeable;

    public function __construct(
        public readonly int $userId,
        public readonly string $path,
        public readonly BotState $state,
        public readonly bool $keyboard,
        public readonly array $actions,
    )
    {
    }

//    public function __serialize(): array
//    {
//        return [
//            'userId' => $this->userId,
//            'path' => $this->path,
//            'state' => $this->state,
//            'keyboard' => $this->keyboard,
//            'actions' => $this->actions,
//        ];
//    }
//
//    public function __unserialize(array $data): void
//    {
//
//    }
}
