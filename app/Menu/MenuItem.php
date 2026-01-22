<?php

namespace App\Menu;

use Closure;
use Countable;
use Domain\TelegramBot\BotState;
use IteratorAggregate;
use Support\Traits\Makeable;

class MenuItem implements Countable, IteratorAggregate, MenuContract
{
    use MenuTrait;

    /** @var MenuItem[] */
    protected array $items = [];
    protected static string $defaultState;

    public function __construct(
        protected string $link,
        protected string $label,
        protected string|Closure|null $state = null,
    ) {
        if ($state === null) {
            $this->state = static::$defaultState;
        }
    }

    public static function make(string $link, string $label, string|Closure|null $state = null): self
    {
        return new self($link, $label, $state);
    }

    public function isCallback(): bool
    {
        return is_callable($this->state);
    }

    public function getCallback(): Closure
    {
        return $this->state;
    }

    public function link(): string
    {
        return $this->link;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function state(): BotState
    {
        return new $this->state($this->link);
    }

    public static function setDefaultState(string $state): void
    {
        self::$defaultState = $state;
    }

    public function isActive(): bool
    {
        return tuser()->state->getPath() === $this->link();
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
