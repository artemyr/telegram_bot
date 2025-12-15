<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Domain\Menu\Categories\CalendarState;
use Domain\Menu\Categories\MainMenuState;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MenuContract::class, function () {
            MenuItem::setDefaultState(MainMenuState::class);

            return MenuItem::make(troute('home'), 'Главное меню')
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
        });
    }

    public function boot(): void
    {
    }
}
