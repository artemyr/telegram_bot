<?php

namespace Domain\TelegramBot;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class MenuBotState extends BotState
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

        $method = ($this->silent && request('can_send_answer_silent', false))
            ? 'editMessageText'
            : 'sendMessage';

        $bot->{$method}(
            text: $menu->label(),
            reply_markup: $keyboard
        );
    }

    public function handle(Nutgram $bot): ?BotState
    {
        $currentMenuItem = menu()->getCurrentCategoryItem();

        $state = $currentMenuItem->state();

        return new $state();
    }
}
