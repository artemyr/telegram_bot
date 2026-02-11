<?php

namespace Domain\TelegramBot\Factory;

use Support\Traits\Runable;

abstract class AbstractBotFactory
{
    use Runable;

    abstract public function getBotCode(): string;
    abstract public function handle(): void;
}
