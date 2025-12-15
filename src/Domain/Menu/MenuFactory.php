<?php

namespace Domain\Menu;

use App\Telegram\Middleware\AuthMiddleware;
use App\Telegram\Middleware\RequestMiddleware;
use Domain\Menu\Categories\MainMenuState;
use SergiX44\Nutgram\Nutgram;
use Services\TelegramBot\UserStateStore;
use Throwable;

class MenuFactory
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->middleware(AuthMiddleware::class);
        $bot->middleware(RequestMiddleware::class);

        $bot->onCommand('start', function (Nutgram $bot) {
            try {
                $state = new MainMenuState();
                UserStateStore::set($bot->userId(), $state);
                $state->render($bot);
            } catch (Throwable $e) {
                report($e);
                if (app()->hasDebugModeEnabled()) {
                    $bot->sendMessage("Error! " . $e->getMessage());
                }
            }
        });

        $bot->onCallbackQuery(function (Nutgram $bot) {
            try {
                $current = UserStateStore::get($bot->userId()) ?? new MainMenuState();
                $next = $current->handle($bot);

                if ($next) {
                    $next->silent();
                    UserStateStore::set($bot->userId(), $next);
                    $next->render($bot);
                }
            } catch (Throwable $e) {
                report($e);
                if (app()->hasDebugModeEnabled()) {
                    $bot->sendMessage("Error! " . $e->getMessage());
                }
            }
        });

        try {
            if (app()->isLocal()) {
                $bot->registerMyCommands();
            }
        } catch (Throwable $e) {
            report($e);
            if (app()->hasDebugModeEnabled()) {
                $bot->sendMessage("Error! " . $e->getMessage());
            }
        }
    }
}
