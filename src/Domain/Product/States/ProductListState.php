<?php

namespace Domain\Product\States;

use Domain\Product\Models\Product;
use Domain\Product\Presentations\ProductTextTablePresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;

class ProductListState extends BotState
{
    public function render(): void
    {
        $products = Product::query()
            ->where('exist', false)
            ->get();

        message()
            ->text([
                "Раздел: Продукты",
                "Напишите номер продукта, чтобы его отменить купленным",
                "Список продуктов к покупке:",
                (string)(new ProductTextTablePresentation($products))
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    public function handle(): void
    {
        if (schedule_bot()->isCallbackQuery()) {
            $query = schedule_bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $newState = new MenuBotState(troute('home'));
                tuserstate()->changeState($newState);
                return;
            }
        } else {
            $query = schedule_bot()->message()?->getText();

            if (filter_var($query, FILTER_VALIDATE_INT)) {
                $products = Product::query()
                    ->where('exist', false)
                    ->get();

                $table = (new ProductTextTablePresentation($products))->getTable();

                $row = $table->getRow((int)$query);

                if (empty($row)) {
                    throw new PrintableException('Выберите из списка');
                }

                $product = Product::query()
                    ->where('id', $row->getCol('id'))
                    ->first();

                if($product) {
                    $product->exist = true;
                    $product->buy_at = now();
                    $product->save();
                    message("Продукт \"{$product->title}\" куплен");
                } else {
                    message("Продукт не наден");
                }
                return;
            }
        }
    }
}
