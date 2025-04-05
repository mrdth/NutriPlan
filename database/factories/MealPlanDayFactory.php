<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MealPlan;
use App\Models\MealPlanDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MealPlanDay>
 */
class MealPlanDayFactory extends Factory
{
    protected $model = MealPlanDay::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meal_plan_id' => MealPlan::factory(),
            'day_number' => $this->faker->unique()->numberBetween(1, 7), // Default to 7 days, adjust if needed
        ];
    }
}
