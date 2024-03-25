<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lease>
 */
class LeaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $endDate = fake()->dateTimeThisYear();
        $expectedEndDate = fake()->dateTimeThisYear($endDate);
        $startDate = fake()->dateTimeThisYear($expectedEndDate);
        $initialKm = fake()->numberBetween(500, 900);
        $finalKm = fake()->numberBetween($initialKm, 1500);
        return [
            'user_id' => fake()->numberBetween(1,2),
            'car_id' => fake()->numberBetween(1,5),
            'start_date' => $startDate,
            'expected_end_date' => $expectedEndDate,
            'actual_end_date' => $endDate,
            'initial_km' => $initialKm,
            'final_km' => $finalKm
        ];
    }
}
