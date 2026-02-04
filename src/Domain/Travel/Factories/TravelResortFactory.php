<?php

namespace Domain\Travel\Factories;

use Domain\Travel\Models\TravelResort;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelResortFactory extends Factory
{
    protected $model = TravelResort::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'sort' => 500,
        ];
    }
}
