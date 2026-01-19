<?php

namespace Domain\Product\Presentations;

use Domain\Product\Models\Product;
use Domain\TelegramBot\Dto\Table\ColDto;
use Domain\TelegramBot\Dto\Table\RowDto;
use Domain\TelegramBot\Dto\Table\TableDto;
use Illuminate\Support\Collection;

class ProductPresentation
{
    public function __construct(
        protected Collection $products,
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
            $table->addRow($row);
        }

        return $table;
    }
}
