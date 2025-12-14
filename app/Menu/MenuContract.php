<?php

namespace App\Menu;

interface MenuContract {
    public function getCurrentCategoryItem(): self;
    public function getParent(): ?self;
    public function link(): string;
}
