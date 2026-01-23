<?php

namespace Domain\Product\States;

use Domain\Product\Models\Product;
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
                ->where('telegram_user_id', bot()->userId())
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

    public function handle(): void
    {
        if (bot()->isCallbackQuery()) {
            $query = bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $this->transition(new MenuBotState(troute('food')));
                return;
            }

            if ($query === KeyboardEnum::NEXT->value) {
                $this->pagen++;
                $this->save();
                return;
            }

            if ($query === KeyboardEnum::PREV->value) {
                if ($this->pagen === 1) {
                    message()->hint("Начало списка");
                    $this->block = true;
                    $this->save();
                    return;
                }

                $this->pagen--;
                $this->save();
                return;
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
                $this->save();
            } else {
                message()->hint("Продукт не наден");
                $this->block = true;
                $this->save();
            }
            return;
        } else {
            $this->exit();
        }
    }

    protected function save(): void
    {
        $newState = new self($this->path, $this->pagen, $this->block);
        tuser()->changeState($newState);
    }
}
