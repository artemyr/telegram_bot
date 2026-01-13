<?php

namespace Domain\TelegramBot\Exceptions;

use Exception;

class MessageManagerException extends Exception
{
    public static function driverNotSupported(): self
    {
        return new self('Driver not supported');
    }
}
