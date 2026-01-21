<?php

namespace Domain\Product\States;

use Domain\Product\Models\Product;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class ProductListSpoilState extends BotState
{
    protected int $pagen;
    protected bool $block;

    public function __construct(?string $path = null, int $pagen = 1, bool $block = false)
    {
        parent::__construct($path);
        $this->pagen = $pagen;
        $this->block = $block;
    }

    public function render(): void
    {
        if (!$this->block) {
            $products = Product::query()
                ->select('id', 'telegram_user_id', 'exist', 'title')
                ->where('telegram_user_id', schedule_bot()->userId())
                ->where('exist', true)
                ->paginate(10, null, null, $this->pagen);

            $keyboard = keyboard()->pagination();

            foreach ($products as $product) {
                $keyboard[$product->id] = $product->title;
            }

            message()
                ->text([
                    "Раздел: Продукты",
                    "Нажмите, чтобы его отменить продукт закончившимся",
                    "Список продуктов:",
                ])
                ->inlineKeyboard($keyboard)
                ->send();
        }
    }

    public function handle(): void
    {
        if (schedule_bot()->isCallbackQuery()) {
            $query = schedule_bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $newState = new MenuBotState(troute('food'));
                tuserstate()->changeState($newState);
                return;
            }

            if ($query === KeyboardEnum::NEXT->value) {
                $newState = new self(troute('food.spoil'), $this->pagen + 1);
                tuserstate()->changeState($newState);
                return;
            }

            if ($query === KeyboardEnum::PREV->value) {
                if ($this->pagen === 1) {
                    message()->hint("Начало списка");
                    $newState = new self(troute('food.spoil'), $this->pagen, true);
                    tuserstate()->changeState($newState);
                    return;
                }

                $newState = new self(troute('food.spoil'), $this->pagen - 1);
                tuserstate()->changeState($newState);
                return;
            }

            $product = Product::query()
                ->where('id', $query)
                ->first();

            if ($product) {
                $product->exist = false;
                $product->save();
                message()->hint("Продукт \"{$product->title}\" закончился");
            } else {
                message()->hint("Продукт не наден");
            }
            return;
        } else {
            message("Используйте кнопки");
        }
    }
}
