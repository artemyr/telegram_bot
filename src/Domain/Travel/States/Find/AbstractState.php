<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;

abstract class AbstractState extends BotState
{
    protected function validate(string $value, array $keyboard): bool
    {
        $variants = [];

        foreach ($keyboard as $item) {
            if (is_array($item)) {
                foreach ($item as $button) {
                    $variants[] = $button;
                }
            } else {
                $variants[] = $item;
            }
        }

        return in_array($value, $variants, true);
    }
}
