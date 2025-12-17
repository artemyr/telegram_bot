<?php

namespace App\Menu;

use Illuminate\Support\Collection;

interface MenuContract {
    public function getCurrentCategoryItem(): self;
    public function getParent(): ?self;
    public function link(): string;
    public function label(): string;
    public function state(): string;
    public function all(): Collection;
}
