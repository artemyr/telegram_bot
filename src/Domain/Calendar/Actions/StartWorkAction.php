<?php

namespace Domain\Calendar\Actions;

class StartWorkAction
{
    public function __invoke(): void
    {
        bot()->sendMessage('work!!');
    }
}
