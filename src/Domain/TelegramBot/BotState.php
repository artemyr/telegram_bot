<?php

namespace Domain\TelegramBot;

use SergiX44\Nutgram\Nutgram;

abstract class BotState
{
    public bool $silent = false;

    public function silent(bool $silent = true): static
    {
        $this->silent = $silent;
        return $this;
    }

    abstract public function render(Nutgram $bot): void;

    public function handle(Nutgram $bot): ?BotState
    {
        return null;
    }

    protected function transition(Nutgram $bot, BotState $state): BotState
    {
        return $state;
    }
}
