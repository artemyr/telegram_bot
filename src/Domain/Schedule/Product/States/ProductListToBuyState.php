<?php

namespace Domain\Schedule\Product\States;

use Domain\Schedule\Product\Models\Product;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class ProductListToBuyState extends BotState
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
                ->where('telegram_user_id', nutgram()->userId())
                ->where('exist', false)
                ->paginate(10, null, null, $this->pagen);

            $keyboard = keyboard()->pagination();

            foreach ($products as $product) {
                $keyboard[$product->id] = $product->title;
            }

            message()
                ->text([
                    "Раздел: Продукты",
                    "Нажмите, чтобы отменить продукт купленным",
                    "Список продуктов к покупке:",
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
                return new MenuBotState(troute('food'));
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
                $product->exist = true;
                $product->buy_at = now();
                $product->save();
                message()->hint("Продукт \"{$product->title}\" куплен");
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
