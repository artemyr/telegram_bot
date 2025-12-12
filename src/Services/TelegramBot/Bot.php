<?php

namespace Services\TelegramBot;

use SergiX44\Nutgram\Nutgram;
use Services\TelegramBot\Menu\MainMenuState;

class Bot
{
    public function __invoke(Nutgram $bot)
    {
        $bot->onCommand('start', function (Nutgram $bot) {
            $state = new MainMenuState();
            UserStateStore::set($bot->userId(), $state);
            $state->render($bot);
        });

        $bot->onCallbackQuery(function (Nutgram $bot) {
            $current = UserStateStore::get($bot->userId()) ?? new MainMenuState();

            $next = $current->handle($bot);

            if ($next) {
                UserStateStore::set($bot->userId(), $next);
                $next->render($bot);
            }
        });
    }
}
