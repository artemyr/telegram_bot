<?php

namespace Domain\TelegramBot;

abstract class BotState
{
    public bool $silent = false;

    public function silent(bool $silent = true): static
    {
        $this->silent = $silent;
        return $this;
    }

    abstract public function render(): void;

    public function handle(): ?BotState
    {
        return null;
    }

    protected function transition(BotState $state): BotState
    {
        return $state;
    }
}
