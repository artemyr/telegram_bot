<?php

namespace App\Menu;

use Closure;
use Countable;
use Illuminate\Support\Str;
use IteratorAggregate;

class MenuItem implements Countable, IteratorAggregate, MenuContract
{
    use MenuTrait;

    protected static string $currentPath;
    protected static $defaultTarget;

    /** @var MenuItem[] */
    protected array $items = [];
    protected string $link;
    protected null|Closure|string $target;

    public function __construct(protected string $label) {
    }

    public static function setCurrentPath(string $path): void
    {
        self::$currentPath = $path;
    }

    public static function setDefaultTarget($target): void
    {
        self::$defaultTarget = $target;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function isCallback(): bool
    {
        return is_callable($this->target());
    }

    public function link(): string
    {
        if (empty($this->link)) {
            return Str::slug($this->label, '_');
        }

        return $this->link;
    }

    public function label(): string
    {
        if (empty($this->label)) {
            return $this->link;
        }

        return $this->label;
    }

    public function target(): string|Closure|null
    {
        if (empty($this->target)) {
            return self::$defaultTarget;
        }

        return $this->target;
    }

    public function isActive(): bool
    {
        return self::$currentPath === $this->link();
    }

    public function getCurrentCategoryItem(): self
    {
        $item = $this->recurseSearch($this);

        if (empty($item)) {
            $item = menu();
        }

        return $item;
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

    public function setPath(string $route): self
    {
        $this->link = $route;
        return $this;
    }

    public function setTarget($target): self
    {
        $this->target = $target;
        return $this;
    }

    public function items(array $items): self
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }
}
