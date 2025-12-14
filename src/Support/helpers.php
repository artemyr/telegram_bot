<?php

use App\Menu\MenuContract;

if (!function_exists('menu')) {
    function menu(): MenuContract
    {
        return app(MenuContract::class);
    }
}
