<?php

namespace Domain\TelegramBot;

use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Exceptions\BotMenuException;
use Domain\TelegramBot\Facades\Keyboard;
use Domain\TelegramBot\Facades\UserState;

class MenuBotState extends BotState
{
    public function render(): void
    {
        $menu = menu()->getCurrentCategoryItem();

        $buttons = [];

        foreach ($menu->all() as $category) {
            $buttons[] = $category->label();
        }

        if ($parent = $menu->getParent()) {
            $buttons[] = KeyboardContract::BACK;
        }

        bot()->sendMessage(
            text: $menu->label(),
            reply_markup: Keyboard::markup($buttons)
        );

        UserState::changeKeyboard(bot()->userId(), true);
    }

    /**
     * @throws BotMenuException
     */
    public function handle(): ?BotState
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();
        $found = false;
        $text = bot()->message()?->getText();

        if (!empty($text)) {
            if ($text === KeyboardContract::BACK) {
                $found = true;
                if ($currentMenuItem->getParent()) {
                    UserState::changePath(bot()->userId(), $currentMenuItem->getParent()->link());
                    $currentMenuItem = $currentMenuItem->getParent();
                } else {
                    logger()->warning('Button back not handled on path' . tuser()->path);
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    if ($item->label() === $text) {
                        UserState::changePath(bot()->userId(), $item->link());
                        $currentMenuItem = $item;
                        $found = true;
                    }
                }
            }
        }

        if (!$found) {
            bot()->sendMessage('Выберите значение из списка');
        }

        $state = $currentMenuItem->state();

        return new $state();
    }
}
