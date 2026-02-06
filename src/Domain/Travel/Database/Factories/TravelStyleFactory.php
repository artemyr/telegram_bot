<?php

namespace Domain\Travel\Database\Factories;

use Domain\Travel\Models\TravelStyle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelStyleFactory extends Factory
{
    protected $model = TravelStyle::class;

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
