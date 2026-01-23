<?php

namespace App\Http\Controllers\Telegram;

use Domain\TelegramBot\Exceptions\PrintableException;

trait TelegramStateTrait
{
    protected function handleState(): void
    {
        try_to(function () {
            $userDto = tuser()->get();

            $current = $userDto->state;
            $current->handle();

            $userDto = tuser()->get();
            $next = $userDto->state;

            $next?->render();
        }, function ($e) {
            $this->handleException($e);
        });
    }

    protected function handleException($e): void
    {
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
    }
}
