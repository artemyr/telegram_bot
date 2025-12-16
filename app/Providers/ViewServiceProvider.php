<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use App\Telegram\States\CalendarAddState;
use App\Telegram\States\CalendarListState;
use Domain\TelegramBot\MenuBotState;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MenuContract::class, function () {
            MenuItem::setDefaultState(MenuBotState::class);

            return MenuItem::make(troute('home'), 'Главное меню')
                ->add(MenuItem::make(troute('calendar'), 'Календарь')
                    ->add(MenuItem::make(troute('calendar.add'), 'Отмеитить событие', CalendarAddState::class))
                    ->add(MenuItem::make(troute('calendar.list'), 'Список событий', CalendarListState::class))
                )
                ->add(MenuItem::make(troute('food'), 'Еда'));
        });
    }

    public function boot(): void
    {
    }
}
