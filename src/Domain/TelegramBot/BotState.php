<?php

namespace Domain\TelegramBot;

abstract class BotState
{
    public function __construct(
        protected ?string $path = null
    )
    {
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    abstract public function render(): void;

    public function handle(): BotState
    {
        return $this;
    }
}
