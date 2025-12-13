<?php

namespace Domain\Menu;

use Domain\Menu\Categories\MainMenuState;
use SergiX44\Nutgram\Nutgram;
use Services\TelegramBot\UserStateStore;

class MenuFactory
{
    public function __invoke(Nutgram $bot): void
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
