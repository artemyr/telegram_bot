<?php

namespace Domain\Travel\Menu;

use App\Menu\MenuItem;
use Domain\Travel\States\Find\StartState;
use Support\Traits\Createable;

class MenuFactory
{
    use Createable;

    public function handle(): MenuItem
    {
        return MenuItem::make(troute('home'), 'Главное меню')
            ->add(MenuItem::make(troute('travel.find'), '🔍 Найти компанию', StartState::class))
            ->add(MenuItem::make(troute('travel.create'), '➕ Создать предложение', fn() => message()->hint('➕ Создать предложение')))
            ->add(MenuItem::make(troute('travel.profile'), '👤 Мой профиль', fn() => message()->hint('👤 Мой профиль')))
            ->add(MenuItem::make(troute('travel.how_work'), '❓ Как это работает', fn() => message()->hint('❓ Как это работает')));
    }
}
