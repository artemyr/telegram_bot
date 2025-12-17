<?php

namespace App\Providers;

use App\Menu\MenuContract;
use App\Menu\MenuItem;
use Domain\Calendar\States\CalendarAddState;
use Domain\Calendar\States\CalendarListState;
use Domain\Settings\States\TimezoneState;
use Domain\Tasks\States\TaskAddState;
use Domain\Tasks\States\TaskListState;
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
                    ->add(MenuItem::make(troute('calendar.add'), 'Отметить событие', CalendarAddState::class))
                    ->add(MenuItem::make(troute('calendar.list'), 'Список событий', CalendarListState::class))
                )
                ->add(MenuItem::make(troute('tasks'), 'Задачи')
                    ->add(MenuItem::make(troute('tasks.list'), 'Список задач', TaskListState::class))
                    ->add(MenuItem::make(troute('tasks.add'), 'Добавить задачу', TaskAddState::class))
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
