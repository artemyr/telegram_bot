<?php

namespace Domain\TelegramBot\Dto\Table;

readonly class ColDto
{
    public function __construct(
        public ?string $value = '',
        public ?string $code = '',
        public bool $hidden = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
