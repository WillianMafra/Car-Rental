<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_model_id' => fake()->numberBetween(1,7),
            'plate' => fake()->currencyCode().fake()->randomNumber(4, true),
            'avaliable' => fake()->boolean(),
            'daily_rate' => fake()->randomFloat(2, 20, 45),
            'km' => fake()->numberBetween(10, 100000)
        ];
    }
}
