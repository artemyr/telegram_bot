<?php

use App\Menu\MenuContract;

if (!function_exists('menu')) {
    function menu(): MenuContract
    {
        return app(MenuContract::class);
    }
}

if (!function_exists('troute')) {
    function troute(string $name, array $parameters = []): string
    {
        $route = route($name, $parameters);

        $route = str_replace(env('APP_URL'), '', $route);
        if (empty($route)) {
            $route = '/';
        }

        return $route;
    }
}
