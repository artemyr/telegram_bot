<?php

namespace Domain\Schedule\Menu;

use App\Menu\MenuItem;
use Domain\Schedule\Calendar\States\CalendarAddState;
use Domain\Schedule\Calendar\States\CalendarListState;
use Domain\Schedule\Product\States\ProductAddState;
use Domain\Schedule\Product\States\ProductListSpoilState;
use Domain\Schedule\Product\States\ProductListState;
use Domain\Schedule\Product\States\ProductListToBuyState;
use Domain\Schedule\Settings\States\TimezoneState;
use Domain\Schedule\Tasks\States\TaskAddState;
use Domain\Schedule\Tasks\States\TaskListState;
use Domain\Schedule\Tasks\States\TaskRecurringAddState;
use Domain\Schedule\Tasks\States\TaskRecurringListState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Support\Traits\Createable;

class MenuFactory
{
    use Createable;

    public function handle(): MenuItem
    {
        return MenuItem::make(troute('home'), 'Главное меню')
            ->add(MenuItem::make(troute('calendar'), '📅 Календарь')
                ->add(MenuItem::make(troute('calendar.add'), '➕ Отметить событие', CalendarAddState::class))
                ->add(MenuItem::make(troute('calendar.list'), '📋 Список событий', CalendarListState::class))
            )
            ->add(MenuItem::make(troute('tasks'), '✅ Задачи')
                ->add(MenuItem::make(troute('tasks.list'), '✅ Список задач', TaskListState::class))
                ->add(MenuItem::make(troute('tasks.add'), '➕ Добавить задачу', TaskAddState::class))
                ->add(MenuItem::make(troute('tasks.recurrence.list'), '✅ Список повторяющихся задач', TaskRecurringListState::class))
                ->add(MenuItem::make(troute('tasks.recurrence.add'), '➕ Добавить повторяющуюся задачу', TaskRecurringAddState::class))
            )
            ->add(MenuItem::make(troute('food'), '🍗 Еда')
                ->add(MenuItem::make(troute('food.to_buy'), '🛒 Покупаю', ProductListToBuyState::class))
                ->add(MenuItem::make(troute('food.spoil'), '🤢 Закончилось', ProductListSpoilState::class))
                ->add(MenuItem::make(troute('food.add'), '➕ Добавить', ProductAddState::class))
                ->add(MenuItem::make(troute('food.list'), '✅ Список', ProductListState::class))
            )
            ->add(
                MenuItem::make(troute('settings'), '⚙️ Настройки')
                    ->add(MenuItem::make(troute('work.start'), 'Начать день', fn() => Cache::set('start_day', true)))
                    ->add(MenuItem::make(troute('work.end'), 'Закончить день', fn() => Cache::set('end_day', true)))
                    ->add(MenuItem::make(troute('work.test'), 'Тест', fn() => Cache::set('work_test', true)))
                    ->add(MenuItem::make(troute('notifications.recreate'), 'Пересоздать мои напоминания по задачам', fn() => Artisan::call('bot:user:notifications:recreate')))
                    ->add(MenuItem::make(troute('timezone'), '🕒 Часовой пояс', TimezoneState::class))
                    ->add(MenuItem::make(troute('webhook_off'), 'Отключить webhook', fn() => Artisan::call('bot:t:hook:remove')))
            );
    }
}
