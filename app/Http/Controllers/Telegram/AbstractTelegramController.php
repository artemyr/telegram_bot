<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Domain\TelegramBot\Exceptions\PrintableException;

abstract class AbstractTelegramController extends Controller
{
    protected function handleState(): void
    {
        $userDto = tuser();

        $current = $userDto->state;
        $next = $current->handle();

        if ($next) {
            tuserstate()->changeState($next);
            $next->render();
        }
    }

    protected function try(callable $call): void
    {
        try_to($call, function ($e) {

            if ($e instanceof PrintableException) {
                message()->text($e->getMessage())->send();
                return;
            }

            if (app()->hasDebugModeEnabled()) {
                message()->text($e->getMessage("Error! " . $e->getMessage()))->send();
            } else {
                message()->text($e->getMessage("Произошла ошибка"))->send();
            }

            report($e);
        });
    }
}
