<?php

namespace Domain\TelegramBot;

abstract class BotState
{
    abstract public function render(): void;

    public function handle(): BotState
    {
        return $this;
    }
}
