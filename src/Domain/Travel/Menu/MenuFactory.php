<?php

namespace Domain\Travel\Menu;

use App\Menu\MenuItem;
use Support\Traits\Createable;

class MenuFactory
{
    use Createable;

    public function handle(): MenuItem
    {
        return MenuItem::make(troute('home'), 'Главное меню')
            ->add(MenuItem::make(troute('travel_find'), '🔍 Найти компанию')
                ->add(MenuItem::make(troute('travel_khutor'), 'Роза хутор'))
                ->add(MenuItem::make(troute('travel_red'), 'Красная поляна'))
                ->add(MenuItem::make(troute('travel_gas'), 'Газпром'))
                ->add(MenuItem::make(troute('travel_sher'), 'Шерегеш'))
                ->add(MenuItem::make(troute('travel_other'), 'Другое (?)'))
            )
            ->add(MenuItem::make(troute('travel_create'), '➕ Создать предложение', fn() => message()->hint('➕ Создать предложение')))
            ->add(MenuItem::make(troute('travel_profile'), '👤 Мой профиль', fn() => message()->hint('👤 Мой профиль')))
            ->add(MenuItem::make(troute('travel_how_work'), '❓ Как это работает', fn() => message()->hint('❓ Как это работает')));
    }
}
