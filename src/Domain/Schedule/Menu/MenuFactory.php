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
        return MenuItem::make('Главное меню')
            ->setPath(troute('home'))
            ->items([
                MenuItem::make('📅 Календарь')
                    ->setPath(troute('schedule.calendar'))
                    ->items([
                        MenuItem::make('➕ Отметить событие')
                            ->setTarget(CalendarAddState::class),
                        MenuItem::make('📋 Список событий')
                            ->setTarget(CalendarListState::class)
                    ]),
                MenuItem::make('✅ Задачи')
                    ->setPath(troute('schedule.tasks'))
                    ->items([
                        MenuItem::make('✅ Список задач')
                            ->setTarget(TaskListState::class),
                        MenuItem::make('➕ Добавить задачу')
                            ->setTarget(TaskAddState::class),
                        MenuItem::make('✅ Список повторяющихся задач')
                            ->setTarget(TaskRecurringListState::class),
                        MenuItem::make('➕ Добавить повторяющуюся задачу')
                            ->setTarget(TaskRecurringAddState::class),
                    ]),
                MenuItem::make('🍗 Еда')
                    ->setPath(troute('schedule.food'))
                    ->items([
                        MenuItem::make('🛒 Покупаю')
                            ->setTarget(ProductListToBuyState::class),
                        MenuItem::make('🤢 Закончилось')
                            ->setTarget(ProductListSpoilState::class),
                        MenuItem::make('➕ Добавить')
                            ->setTarget(ProductAddState::class),
                        MenuItem::make('✅ Список')
                            ->setTarget(ProductListState::class),
                    ]),
                MenuItem::make('⚙️ Настройки')
                    ->setPath(troute('schedule.settings'))
                    ->items([
                        MenuItem::make('Начать день')
                            ->setTarget(fn() => Cache::set('start_day', true)),
                        MenuItem::make('Закончить день')
                            ->setTarget(fn() => Cache::set('end_day', true)),
                        MenuItem::make('Тест')
                            ->setTarget(fn() => Cache::set('work_test', true)),
                        MenuItem::make('Пересоздать мои напоминания по задачам')
                            ->setTarget(fn() => Artisan::call('bot:user:notifications:recreate')),
                        MenuItem::make('🕒 Часовой пояс')
                            ->setTarget(TimezoneState::class),
                        MenuItem::make('Отключить webhook')
                            ->setTarget(fn() => Artisan::call('bot:t:hook:remove')),
                    ])
            ]);
    }
}
