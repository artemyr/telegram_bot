<?php

namespace Domain\Schedule\Product\States;

use Domain\Schedule\Product\Models\Product;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class ProductAddState extends BotState
{
    public const TITLE_STAGE = 'title';
    public const DAYS_STAGE = 'days';

    public function __construct(
        protected ?string $stage = null,
        protected ?int $productId = null,
    ) {
        if (empty($this->stage)) {
            $this->stage = self::TITLE_STAGE;
        }
    }

    public function render(): void
    {
        if ($this->stage === self::TITLE_STAGE) {
            message()
                ->text([
                    "Раздел: Продуктов",
                    "Добавить продукт",
                    "Введите название:",
                ])
                ->inlineKeyboard(keyboard()->back())
                ->send();
        }

        if ($this->stage === self::DAYS_STAGE) {
            message()
                ->text([
                    "Введите количество дней (срок годности)",
                ])
                ->inlineKeyboard(keyboard()->back())
                ->send();
        }
    }

    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                return new MenuBotState(troute('schedule.food'));
            }
        } else {
            $query = nutgram()->message()?->getText();

            if ($this->stage === self::TITLE_STAGE) {
                $product = Product::query()->create([
                    'telegram_user_id' => nutgram()->userId(),
                    'title' => $query,
                ]);
                $this->productId = $product->id;
                $this->stage = self::DAYS_STAGE;

                return $this;
            }

            if ($this->stage === self::DAYS_STAGE) {
                $product = Product::query()
                    ->where('id', $this->productId)
                    ->first();

                $product->expire_days = $query;
                $product->save();

                $this->stage = self::TITLE_STAGE;
                return $this;
            }
        }
    }
}
