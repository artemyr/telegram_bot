<?php

namespace Domain\TelegramBot;

use App\Menu\MenuItem;
use Domain\TelegramBot\Enum\KeyboardEnum;

class MenuBotState extends BotState
{
    public function __construct(
        protected ?string $path = null
    ) {
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

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
            return $this;
        }

        $query = nutgram()->callbackQuery()->data;

        if (!empty($query)) {
            if ($query === KeyboardEnum::BACK->value) {
                if ($currentMenuItem->getParent()) {
                    return new MenuBotState($currentMenuItem->getParent()->link());
                } else {
                    return new MenuBotState(troute('home'));
                }
            } else {
                foreach ($currentMenuItem->all() as $item) {
                    /** @var $item MenuItem */
                    if ($item->link() === $query) {
                        $target = $item->target();

                        if (!empty($target)) {
                            if ($item->isCallback()) {
                                message("Выполнение \"{$item->label()}\"");
                                $target();
                                return $this;
                            } else {
                                if ($target === self::class) {
                                    return new $target($item->link());
                                }

                                return new $target();
                            }
                        }
                    }
                }
            }
        }

        message('Выберите значение из списка');
        return $this;
    }
}
