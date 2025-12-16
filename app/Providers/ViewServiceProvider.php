<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Domain\Calendar\States\CalendarAddState;
use Domain\Calendar\States\CalendarListState;
use Domain\Settings\States\TimezoneState;
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
                ->add(MenuItem::make(troute('food'), 'Еда'))
                ->add(
                    MenuItem::make(troute('settings'), 'Настройки')
                        ->add(MenuItem::make(troute('timezone'), 'Часовой пояс', TimezoneState::class))
                );
        });
    }

    public function boot(): void
    {
    }
}
