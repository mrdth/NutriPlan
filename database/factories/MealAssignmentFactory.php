<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MealAssignment;
use App\Models\MealPlanDay;
use App\Models\MealPlanRecipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MealAssignment>
 */
class MealAssignmentFactory extends Factory
{
    protected $model = MealAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Note: meal_plan_recipe_id should ideally be passed in when creating
        // using the factory, as creating one via its own factory is incorrect.
        // This provides a basic default.
        $mealPlanDay = MealPlanDay::factory()->create();

        return [
            'meal_plan_day_id' => $mealPlanDay->id,
            // Provide a placeholder or ensure the actual ID is passed during test setup
            'meal_plan_recipe_id' => function () use ($mealPlanDay) {
                // Attempt to find or create a valid MealPlanRecipe associated with the same MealPlan
                $mealPlan = $mealPlanDay->mealPlan;
                $recipe = \App\Models\Recipe::factory()->create();
                $mealPlan->recipes()->attach($recipe->id, ['scale_factor' => 1.0]);
                $pivot = $mealPlan->recipes()->where('recipe_id', $recipe->id)->first()->pivot;
                // Manually update servings_available
                $pivot->servings_available = ($recipe->servings * $pivot->scale_factor) / $mealPlan->people_count;
                $pivot->save();
                return $pivot->id;
            },
            'servings' => $this->faker->randomFloat(2, 0.5, 3.0),
        ];
    }
}
