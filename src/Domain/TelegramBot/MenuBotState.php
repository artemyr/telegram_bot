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
                    $newState = new MenuBotState($currentMenuItem->getParent()->link());
                    UserState::changeState(bot()->userId(), $newState);

                    $currentMenuItem = $currentMenuItem->getParent();
                } else {
                    logger()->warning('Button back not handled on path' . tuser()->state->getPath());
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    if ($item->label() === $text) {
                        $newState = new MenuBotState($item->link());
                        UserState::changeState(bot()->userId(), $newState);

                        $currentMenuItem = $item;
                        $found = true;
                    }
                }
            }
        }

        if (!$found) {
            bot()->sendMessage('Выберите значение из списка');
        }

        return $currentMenuItem->state();
    }
}
