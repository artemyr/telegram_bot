<?php

namespace App\Menu;

use Illuminate\Support\Collection;
use Traversable;

trait MenuTrait
{
    protected ?MenuItem $parent;

    public function getParent(): ?self
    {
        if (empty($this->parent)) {
            return null;
        }

        return $this->parent;
    }

    public function all(): Collection
    {
        return Collection::make($this->items);
    }

    public function add(MenuItem $item): self
    {
        $item->parent = $this;
        $this->items[] = $item;

        return $this;
    }

    public function addIf(bool|callable $condition, MenuItem $item): self
    {
        if (is_callable($condition) ? $condition() : $condition) {
            $this->add($item);
        }

        return $this;
    }

    public function remove(MenuItem $item): self
    {
        $this->items = $this->all()
            ->filter(fn (MenuItem $current) => $item !== $current)
            ->toArray();

        return $this;
    }

    public function removeByLink(string $link): self
    {
        $this->items = $this->all()
            ->filter(fn (MenuItem $current) => $link === $current->link())
            ->toArray();

        return $this;
    }

    public function getIterator(): Traversable
    {
        return $this->all();
    }

    public function count(): int
    {
        return count($this->items);
    }
}
