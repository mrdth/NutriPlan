<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MealAssignment;
use App\Models\MealPlan;
use App\Models\MealPlanDay;
use App\Models\MealPlanRecipe;
use App\Models\Recipe;
use App\Models\User;

function setupMealAssignmentTestData(): array
{
    $user = User::factory()->create();
    $mealPlan = MealPlan::factory()->create(['user_id' => $user->id, 'people_count' => 2]);
    $recipe = Recipe::factory()->create(['servings' => 6]);

    // Attach recipe to meal plan to create the MealPlanRecipe pivot record
    $mealPlan->recipes()->attach($recipe->id, ['scale_factor' => 1.0]);
    $mealPlanRecipe = $mealPlan->recipes()->first()->pivot; // Get the created pivot model
    // Manually update servings_available after attaching, as the model event might not run in this context
    $mealPlanRecipe->servings_available = ($recipe->servings * $mealPlanRecipe->scale_factor) / $mealPlan->people_count;
    $mealPlanRecipe->save();

    $mealPlanDay = MealPlanDay::factory()->create(['meal_plan_id' => $mealPlan->id, 'day_number' => 1]);

    return compact('user', 'mealPlan', 'recipe', 'mealPlanDay', 'mealPlanRecipe');
}

test('user can assign recipe to day', function () {
    $data = setupMealAssignmentTestData();
    /** @var User $user */
    $user = $data['user'];
    /** @var MealPlanDay $mealPlanDay */
    $mealPlanDay = $data['mealPlanDay'];
    /** @var MealPlanRecipe $mealPlanRecipe */
    $mealPlanRecipe = $data['mealPlanRecipe'];

    $initialServings = $mealPlanRecipe->servings_available;
    $assignedServings = 1.5;

    $response = $this->actingAs($user)->post(route('meal-assignments.store'), [
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => $assignedServings,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('meal_assignments', [
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => $assignedServings,
    ]);

    // Check that servings_available was updated
    $mealPlanRecipe->refresh();
    expect((float) $mealPlanRecipe->servings_available)->toBe($initialServings - $assignedServings);
});

test('user cannot assign more servings than available', function () {
    $data = setupMealAssignmentTestData();
    /** @var User $user */
    $user = $data['user'];
    /** @var MealPlanDay $mealPlanDay */
    $mealPlanDay = $data['mealPlanDay'];
    /** @var MealPlanRecipe $mealPlanRecipe */
    $mealPlanRecipe = $data['mealPlanRecipe'];

    $assignedServings = $mealPlanRecipe->servings_available + 1; // More than available

    $response = $this->actingAs($user)->post(route('meal-assignments.store'), [
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => $assignedServings,
    ]);

    // Expect a redirect back with validation errors
    $response->assertRedirect();
    $response->assertSessionHasErrors('servings');
    $this->assertDatabaseMissing('meal_assignments', [
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
    ]);
});

test('user can update meal assignment', function () {
    $data = setupMealAssignmentTestData();
    /** @var User $user */
    $user = $data['user'];
    /** @var MealPlanDay $mealPlanDay */
    $mealPlanDay = $data['mealPlanDay'];
    /** @var MealPlanRecipe $mealPlanRecipe */
    $mealPlanRecipe = $data['mealPlanRecipe'];

    $assignment = MealAssignment::factory()->create([
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => 1.0,
    ]);
    // Adjust available servings after creating the initial assignment
    $mealPlanRecipe->servings_available -= $assignment->servings;
    $mealPlanRecipe->save();
    $initialServings = $mealPlanRecipe->servings_available;

    $updatedServings = $initialServings + 0.5; // Update requires available servings

    $response = $this->actingAs($user)->put(route('meal-assignments.update', $assignment->id), [
        'servings' => $updatedServings,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('meal_assignments', [
        'id' => $assignment->id,
        'servings' => $updatedServings,
    ]);

    // Check that servings_available was updated correctly
    $mealPlanRecipe->refresh();
    // Available should be initial - (updated - original assignment servings)
    expect((float) $mealPlanRecipe->servings_available)->toBe($initialServings - ($updatedServings - $assignment->servings));
});

test('user can remove meal assignment', function () {
    $data = setupMealAssignmentTestData();
    /** @var User $user */
    $user = $data['user'];
    /** @var MealPlanDay $mealPlanDay */
    $mealPlanDay = $data['mealPlanDay'];
    /** @var MealPlanRecipe $mealPlanRecipe */
    $mealPlanRecipe = $data['mealPlanRecipe'];

    $assignment = MealAssignment::factory()->create([
        'meal_plan_day_id' => $mealPlanDay->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => 1.0,
    ]);
    // Adjust available servings after creating the initial assignment
    $mealPlanRecipe->servings_available -= $assignment->servings;
    $mealPlanRecipe->save();
    $initialServings = $mealPlanRecipe->servings_available;

    $response = $this->actingAs($user)->delete(route('meal-assignments.destroy', $assignment->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('meal_assignments', ['id' => $assignment->id]);

    // Check that servings_available was restored
    $mealPlanRecipe->refresh();
    expect((float) $mealPlanRecipe->servings_available)->toBe($initialServings + $assignment->servings);
});
