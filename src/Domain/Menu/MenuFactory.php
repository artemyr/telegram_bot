<?php

namespace Domain\Menu;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\Menu\Categories\MainMenuState;
use SergiX44\Nutgram\Nutgram;
use Services\TelegramBot\UserStateStore;

class MenuFactory
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(RequestMiddleware::class);

        $bot->onCommand('start', function (Nutgram $bot) {
            $state = new MainMenuState();
            UserStateStore::set($bot->userId(), $state);
            $state->render($bot);
        });

        $bot->onCallbackQuery(function (Nutgram $bot) {
            $current = UserStateStore::get($bot->userId()) ?? new MainMenuState();
            $next = $current->handle($bot);

            if ($next) {
                $next->silent();
                UserStateStore::set($bot->userId(), $next);
                $next->render($bot);
            }
        });

        if (app()->isLocal()) {
            $bot->registerMyCommands();
        }
    }
}
