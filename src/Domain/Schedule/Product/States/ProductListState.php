<?php

namespace Domain\Schedule\Product\States;

use Domain\Schedule\Product\Models\Product;
use Domain\Schedule\Product\Presentations\ProductTextTablePresentation;
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
    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                return new MenuBotState(troute('food'));
            }
        } else {
            $text = nutgram()->message()?->getText();

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

        return $this;
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
            ->where('telegram_user_id', nutgram()->userId())
            ->get();
    }
}
