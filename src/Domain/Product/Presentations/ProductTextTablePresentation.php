<?php

namespace Domain\Product\Presentations;

use Domain\Product\Models\Product;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProductTextTablePresentation
{
    public function __construct(
        protected Collection $products,
        protected ?string $timezone = null
    )
    {
    }

    public function __toString(): string
    {
        return (string)$this->getTable();
    }

    public function getTable(): TableDto
    {
        $table = new TableDto();
        foreach ($this->products as $product) {
            /** @var Product $product */

            $row = new RowDto();
            $row->addCol(new ColDto($product->id, 'id', true));
            $row->addCol(new ColDto($product->title, 'title'));

            /** @var Carbon $date */
            $date = $product->buy_at;

            if (!$product->exist) {
                $row->addCol(new ColDto("🚫", 'color'));
            }

            if (!empty($date) && $product->exist) {
                $date->setTimezone($this->timezone);
                $row->addCol(new ColDto("($date)", 'buy_at'));

                if (!empty($product->expire_days)) {
                    $row->addCol(new ColDto($this->calculateColor($product), 'color'));
                } else {
                    $row->addCol(new ColDto("❓", 'color'));
                }
            }

            $table->addRow($row);
        }

        return $table;
    }

    protected function calculateColor(Product $product): string
    {
        $expireDays = $product->expire_days;
        $buyAt = $product->buy_at;

        $expireTime = $buyAt->addDays($expireDays);
        $now = now();

        $diff = $now->diffInDays($expireTime);

        $color = '?';

        if ($diff > 1) {
            $color = '🟢';

            $d = ($diff / $expireDays) * 100;
            if ($d < 30) {
                $color = '🟡';
            }
        }

        if ($diff < 1) {
            $color = '🔴';
        }

        return $color;
    }
}
