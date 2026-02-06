<?php

namespace App\Menu;

use Closure;
use Domain\TelegramBot\BotState;
use Illuminate\Support\Collection;

interface MenuContract {
    public function getCurrentCategoryItem(): self;
    public function getParent(): ?self;
    public function link(): string;
    public function label(): string;
    public function target(): string|Closure|null;
    public function all(): Collection;
}
