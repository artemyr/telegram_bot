<?php

namespace Domain\Product\States;

use Domain\Product\Models\Product;
use Domain\Product\Presentations\ProductTextTablePresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Support\Collection;

class ProductListState extends BotState
{
    public function __construct(?string $path = null)
    {
        parent::__construct($path);
    }

    public function render(): void
    {
        message()
            ->text([
                "Раздел: Продукты",
                "Напишите номер продукта чтобы его удалить",
                "🟢 - свежий",
                "🟡 - менее 30 процентов",
                "🔴 - истек",
                "🚫 - нет",
                "❓ - не указан срок",
                (string)(new ProductTextTablePresentation($this->getProducts(), tusertimezone()))
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    /**
     * @throws PrintableException
     */
    public function handle(): void
    {
        if (schedule_bot()->isCallbackQuery()) {
            $query = schedule_bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $this->transition(new MenuBotState(troute('food')));
                return;
            }
        } else {
            $text = schedule_bot()->message()?->getText();

            if (filter_var($text, FILTER_VALIDATE_INT)) {
                $products = $this->getProducts();

                $table = (new ProductTextTablePresentation($products, tusertimezone()))->getTable();
                $row = $table->getRow((int)$text);

                if (empty($row)) {
                    throw new PrintableException('Выберите из списка');
                }

                $id = $row->getCol('id')->value;
                $product = $products->filter(fn($item) => $item->id === (int)$id)->first() ?? null;
                $product?->delete();
            }
        }
    }

    private function getProducts(): Collection
    {
        return Product::query()
            ->select(
                'id',
                'telegram_user_id',
                'exist',
                'title',
                'expire_days',
                'buy_at',
                'store',
            )
            ->orderBy('buy_at')
            ->where('telegram_user_id', schedule_bot()->userId())
            ->get();
    }
}
