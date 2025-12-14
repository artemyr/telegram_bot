<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance(MenuContract::class,
            MenuItem::make('/', 'Главное меню')
                ->add(MenuItem::make('categories', 'Категории')
                    ->add(MenuItem::make('calendar', 'Календарь'))
                    ->add(MenuItem::make('food', 'Еда'))
                )
                ->add(MenuItem::make('settings', 'Настройки')
                    ->add(MenuItem::make('view', 'Вид'))
                    ->add(MenuItem::make('constants', 'Константы')))
        );

    }

    public function boot(): void
    {

    }
}
