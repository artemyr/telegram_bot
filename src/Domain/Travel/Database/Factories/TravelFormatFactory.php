<?php

namespace Domain\Travel\Database\Factories;

use Domain\Travel\Models\TravelFormat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TravelFormatFactory extends Factory
{
    protected $model = TravelFormat::class;

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
