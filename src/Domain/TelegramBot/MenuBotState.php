<?php

namespace Domain\TelegramBot;

use App\Menu\MenuItem;
use Domain\TelegramBot\Enum\KeyboardEnum;

class MenuBotState extends BotState
{
    public function render(): void
    {
        keyboard()->remove();

        $menu = menu()->getCurrentCategoryItem();

        $buttons = [];

        if ($parent = $menu->getParent()) {
            $buttons[KeyboardEnum::BACK->value] = KeyboardEnum::BACK->label();
        }

        foreach ($menu->all() as $category) {
            /** @var MenuItem $category */
            $buttons[$category->link()] = $category->label();
        }

        message()
            ->text($menu->label())
            ->inlineKeyboard($buttons)
            ->send();
    }

    public function handle(): void
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();
        $found = false;

        if (!nutgram()->isCallbackQuery()) {
            message('Используйте кнопки для навигации');
            tuser()->changeState($currentMenuItem->state());
            return;
        }

        $text = nutgram()->callbackQuery()->data;

        if (!empty($text)) {
            if ($text === KeyboardEnum::BACK->value) {
                $found = true;
                if ($currentMenuItem->getParent()) {
                    $newState = new MenuBotState($currentMenuItem->getParent()->link());
                    tuser()->changeState($newState);

                    $currentMenuItem = $currentMenuItem->getParent();
                } else {
                    $this->exit();
                    return;
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    /** @var $item MenuItem */
                    if ($item->link() === $text) {

                        if ($item->isCallback()) {
                            message("Выполнение \"{$item->label()}\"");
                            $call = $item->getCallback();
                            $call();
                            $currentMenuItem = $item->getParent();
                        } else {
                            $newState = new MenuBotState($item->link());
                            tuser()->changeState($newState);

                            $currentMenuItem = $item;
                        }
                        $found = true;
                    }
                }
            }
        }

        if (!$found) {
            message('Выберите значение из списка');
            return;
        }

        $this->transition($currentMenuItem->state());
    }
}
