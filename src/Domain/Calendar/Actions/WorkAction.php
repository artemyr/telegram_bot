<?php

namespace Domain\Calendar\Actions;

class WorkAction
{
    public function __invoke(): void
    {
        bot()->sendMessage('start session');
    }
}
