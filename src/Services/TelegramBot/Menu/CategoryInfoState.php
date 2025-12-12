<?php

namespace Services\TelegramBot\Menu;

use SergiX44\Nutgram\Nutgram;

class CategoryInfoState extends MenuState
{
    public function __construct(protected string $text)
    {
    }

    public function render(Nutgram $bot): void
    {
        $bot->sendMessage($this->text);
    }
}
