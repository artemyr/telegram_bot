<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Domain\Menu\Categories\CalendarState;
use Domain\Menu\Categories\MainMenuState;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Route::name('home')->get('/');
        Route::name('categories')->get('/categories');
        Route::name('calendar')->get('/categories/calendar');
        Route::name('food')->get('/categories/food');
        Route::name('settings')->get('/settings');
        Route::name('view')->get('/settings/view');
        Route::name('constants')->get('/settings/constants');

        MenuItem::setDefaultState(MainMenuState::class);

        $menu = MenuItem::make(troute('home'), 'Главное меню')
            ->add(
                MenuItem::make(troute('categories'), 'Категории')
                    ->add(MenuItem::make(troute('calendar'), 'Календарь', CalendarState::class))
                    ->add(MenuItem::make(troute('food'), 'Еда'))
            )
            ->add(
                MenuItem::make(troute('settings'), 'Настройки')
                    ->add(MenuItem::make(troute('view'), 'Вид'))
                    ->add(MenuItem::make(troute('constants'), 'Константы'))
            );

        $this->app->instance(MenuContract::class, $menu);
    }
}
