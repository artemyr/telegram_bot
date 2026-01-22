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
        $current->handle();

        $userDto = tuser();
        $next = $userDto->state;

        $next?->render();
    }

    protected function try(callable $call): void
    {
        try_to($call, function ($e) {

            if ($e instanceof PrintableException) {
                message($e->getMessage());
                return;
            }

            if (app()->hasDebugModeEnabled()) {
                message("Error! " . $e->getMessage());
            } else {
                message("Произошла ошибка");
            }

            report($e);
        });
    }
}
