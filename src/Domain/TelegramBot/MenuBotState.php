<?php

namespace Domain\TelegramBot;

use App\Menu\MenuItem;
use Domain\TelegramBot\Contracts\KeyboardContract;

class MenuBotState extends BotState
{
    public function render(): void
    {
        keyboard()->remove();

        $menu = menu()->getCurrentCategoryItem();

        $buttons = [];

        if ($parent = $menu->getParent()) {
            $buttons[KeyboardContract::BACK] = KeyboardContract::BACK;
        }

        foreach ($menu->all() as $category) {
            /** @var MenuItem $category */
            $buttons[$category->link()] = $category->label();
        }

        message()
            ->text($menu->label())
            ->inlineKeyboard($buttons)
            ->tryEditLast()
            ->send();

        tuserstate()->changeBlockEditBotMessage(false);
    }

    public function handle(): ?BotState
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();
        $found = false;
//        $text = bot()->message()?->getText();

        if (!bot()->isCallbackQuery()) {
            message('Используйте кнопки для навигации');
            tuserstate()->changeBlockEditBotMessage(true);
            return $currentMenuItem->state();
        }

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

                        if ($item->isCallback()) {
                            $call = $item->getCallback();
                            $call();
                            $currentMenuItem = $item->getParent();
                            $found = true;
                        } else {
                            $newState = new MenuBotState($item->link());
                            tuserstate()->changeState($newState);

                            $currentMenuItem = $item;
                            $found = true;
                        }
                    }
                }
            }
        }

        if (!$found) {
            message('Выберите значение из списка');
            tuserstate()->changeBlockEditBotMessage(true);
        }

        $newState = $currentMenuItem->state();

        if (!$newState instanceof MenuBotState) {
            bot()->message()?->delete();
        }

        return $newState;
    }
}
