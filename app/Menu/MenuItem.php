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
    protected static string $defaultState;

    public function __construct(
        protected string $link,
        protected string $label,
        protected ?string $state = null,
    )
    {
        if ($state === null) {
            $this->state = static::$defaultState;
        }
    }

    public function link(): string
    {
        return $this->link;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function state(): string
    {
        return $this->state;
    }

    public static function setDefaultState(string $state): void
    {
        self::$defaultState = $state;
    }

    public function isActive(): bool
    {
        return tuser()->path === $this->link();
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
