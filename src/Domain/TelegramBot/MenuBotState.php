<?php

namespace Domain\TelegramBot;

use App\Menu\MenuItem;
use Domain\TelegramBot\Contracts\KeyboardContract;
use Domain\TelegramBot\Exceptions\BotMenuException;

class MenuBotState extends BotState
{
    public function render(): void
    {
        $menu = menu()->getCurrentCategoryItem();

        $buttons = [];

        if ($parent = $menu->getParent()) {
            $buttons[KeyboardContract::BACK] = KeyboardContract::BACK;
        }

        foreach ($menu->all() as $category) {
            /** @var MenuItem $category */
            $buttons[$category->link()] = $category->label();
        }

        message()->text($menu->label())->inlineKeyboard($buttons)->send();

        tuserstate()->changeKeyboard(true);
    }

    /**
     * @throws BotMenuException
     */
    public function handle(): ?BotState
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();
        $found = false;
//        $text = bot()->message()?->getText();
        $text = bot()->callbackQuery()->data;

        if (!empty($text)) {
            if ($text === KeyboardContract::BACK) {
                $found = true;
                if ($currentMenuItem->getParent()) {
                    $newState = new MenuBotState($currentMenuItem->getParent()->link());
                    tuserstate()->changeState($newState);

                    $currentMenuItem = $currentMenuItem->getParent();
                } else {
                    logger()->warning('Button back not handled on path' . tuser()->state->getPath());
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    /** @var $item MenuItem */
                    if ($item->link() === $text) {
                        $newState = new MenuBotState($item->link());
                        tuserstate()->changeState($newState);

                        $currentMenuItem = $item;
                        $found = true;
                    }
                }
            }
        }

        if (!$found) {
            message('Выберите значение из списка');
            sleep(2);
        }

        return $currentMenuItem->state();
    }
}
