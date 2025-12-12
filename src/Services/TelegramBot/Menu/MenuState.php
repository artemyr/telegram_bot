<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;

abstract class MenuState
{
    abstract public function render(Nutgram $bot): void;

    public function handle(Nutgram $bot): ?MenuState
    {
        return null;
    }

    protected function transition(Nutgram $bot, MenuState $state): MenuState
    {
        return $state;
    }
}
