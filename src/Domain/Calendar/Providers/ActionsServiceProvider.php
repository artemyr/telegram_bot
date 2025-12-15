<?php

namespace Domain\Calendar\Providers;

use Domain\Calendar\Actions\ShowMenuAction;
use Domain\Calendar\Contracts\ShowMenuContract;
use Illuminate\Support\ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{
    public $bindings = [
        ShowMenuContract::class => ShowMenuAction::class
    ];
}
