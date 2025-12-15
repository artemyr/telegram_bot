<?php

namespace Domain\Calendar\Actions;

class PiAction
{
    public function __invoke(): void
    {
        bot()->sendMessage('pi pi');
    }
}
