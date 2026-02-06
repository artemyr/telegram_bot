<?php

namespace Domain\Schedule\Product\States;

use Domain\Schedule\Product\Models\Product;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class ProductListSpoilState extends BotState
{
    protected int $pagen;
    protected bool $block;

    public function __construct(int $pagen = 1, bool $block = false)
    {
        $this->pagen = $pagen;
        $this->block = $block;
    }

    public function render(): void
    {
        if (!$this->block) {
            $products = Product::query()
                ->select('id', 'telegram_user_id', 'exist', 'title')
                ->where('telegram_user_id', nutgram()->userId())
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

    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                return new MenuBotState(troute('schedule.food'));
            }

            if ($query === KeyboardEnum::NEXT->value) {
                $this->pagen++;
                return $this;
            }

            if ($query === KeyboardEnum::PREV->value) {
                if ($this->pagen === 1) {
                    message()->hint("Начало списка");
                    $this->block = true;
                    return $this;
                }

                $this->pagen--;
                return $this;
            }

            $product = Product::query()
                ->where('id', $query)
                ->first();

            if ($product) {
                $product->exist = false;
                $product->save();
                message()->hint("Продукт \"{$product->title}\" закончился");
                $this->block = false;
            } else {
                message()->hint("Продукт не наден");
                $this->block = true;
            }
            return $this;
        } else {
            return $this;
        }
    }
}
