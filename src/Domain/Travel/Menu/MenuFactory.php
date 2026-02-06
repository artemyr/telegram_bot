<?php

namespace Domain\Travel\Menu;

use App\Menu\MenuItem;
use Domain\Travel\States\Find\StartState;
use Domain\Travel\States\Profile\ProfileState;
use Support\Traits\Createable;

class MenuFactory
{
    use Createable;

    public function handle(): MenuItem
    {
        return MenuItem::make('Главное меню')
            ->setPath(troute('home'))
            ->items([
                MenuItem::make('🔍 Найти компанию')
                    ->setPath(troute('travel.find'))
                    ->setTarget(StartState::class),
                MenuItem::make('➕ Создать предложение')
                    ->setPath(troute('travel.create'))
                    ->setTarget(fn() => message()->hint('➕ Создать предложение')),
                MenuItem::make('👤 Мой профиль')
                    ->setPath(troute('travel.profile'))
                    ->setTarget(ProfileState::class),
                MenuItem::make('❓ Как это работает')
                    ->setPath(troute('travel.how_work'))
                    ->setTarget(fn() => message()->hint('❓ Как это работает')),
            ]);
    }
}
