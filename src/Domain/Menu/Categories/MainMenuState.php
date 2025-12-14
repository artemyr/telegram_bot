<?php

namespace Domain\Menu\Categories;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Services\TelegramBot\Menu\MenuState;

class MainMenuState extends MenuState
{
    public function render(Nutgram $bot): void
    {
        $keyboard = InlineKeyboardMarkup::make();

        $menu = menu()->getCurrentCategoryItem();

        foreach ($menu->all() as $category) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $category->label(),
                    callback_data:  $category->link()
                )
            );
        }

        if ($parent = $menu->getParent()) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: "Назад",
                    callback_data: $parent->link()
                )
            );
        }

        $method = $this->silent
            ? 'editMessageText'
            : 'sendMessage';

        $bot->{$method}(
            text: $menu->label(),
            reply_markup: $keyboard
        );
    }

    public function handle(Nutgram $bot): ?MenuState
    {
//        $currentMenu = menu()->getCurrentCategoryItem();
        return new MainMenuState();
    }
}
