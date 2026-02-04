<?php

namespace Domain\Travel\Seeders;

use Domain\Travel\Models\TravelFormat;
use Domain\Travel\Models\TravelResort;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['title' => 'Роза хутор'],
            ['title' => 'Красная поляна'],
            ['title' => 'Газпром'],
            ['title' => 'Шерегеш'],
        ];

        TravelResort::factory()
            ->count(count($data))
            ->state(new Sequence(...$data))
            ->create();

        $data = [
            ['title' => '🎿 Кататься вместе'],
            ['title' => '🚗 Трансфер'],
            ['title' => '🍻 После каталки'],
        ];

        TravelFormat::factory()
            ->count(count($data))
            ->state(new Sequence(...$data))
            ->create();
    }
}
