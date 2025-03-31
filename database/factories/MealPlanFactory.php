<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealPlan>
 */
class MealPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->optional(0.7)->sentence(3),
            'start_date' => $this->faker->dateTimeBetween('now', '+2 weeks'),
            'duration' => $this->faker->randomElement([7, 14]),
            'people_count' => $this->faker->numberBetween(1, 8),
        ];
    }
}
