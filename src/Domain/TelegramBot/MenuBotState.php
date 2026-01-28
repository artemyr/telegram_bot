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

    public function handle(): BotState
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();

        if (!nutgram()->isCallbackQuery()) {
            message('Используйте кнопки для навигации');
            return $currentMenuItem->state();
        }

        $text = nutgram()->callbackQuery()->data;

        if (!empty($text)) {
            if ($text === KeyboardEnum::BACK->value) {
                if ($currentMenuItem->getParent()) {
                    return new MenuBotState($currentMenuItem->getParent()->link());
                } else {
                    return new MenuBotState(troute('home'));
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    /** @var $item MenuItem */
                    if ($item->link() === $text) {

                        if ($item->isCallback()) {
                            message("Выполнение \"{$item->label()}\"");
                            $call = $item->getCallback();
                            $call();
                        } else {
                            return $item->state();
                        }
                    }
                }
            }
        }

        message('Выберите значение из списка');
        return $this;
    }
}
