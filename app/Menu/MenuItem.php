<?php

namespace App\Menu;

use Countable;
use IteratorAggregate;
use Support\Traits\Makeable;

class MenuItem implements Countable, IteratorAggregate, MenuContract
{
    use Makeable;
    use MenuTrait;

    /** @var MenuItem[] */
    protected array $items = [];

    public function __construct(
        protected string $link,
        protected string $label,
    )
    {
    }

    public function link(): string
    {
        return $this->link;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isActive(): bool
    {
        return request('path') === $this->link();
    }

    public function getCurrentCategoryItem(): self
    {
        return $this->recurseSearch($this);
    }

    protected function recurseSearch($item): ?self
    {
        if ($item->isActive()) {
            return $item;
        }

        foreach ($item->items as $el) {

            /** @var $el MenuItem */
            if ($el->isActive()) {
                return $el;
            }

            if ($el->count()) {
                $search = $el->recurseSearch($el);
                if (!empty($search)) {
                    return $el->recurseSearch($search);
                }
            }
        }

        return null;
    }
}
