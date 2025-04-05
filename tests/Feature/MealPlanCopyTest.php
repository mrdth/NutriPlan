<?php

declare(strict_types=1);

use App\Models\MealAssignment;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;

test('a user can copy their meal plan', function () {
    // Create a user with a meal plan
    $user = User::factory()->create();
    $mealPlan = MealPlan::factory()->create([
        'user_id' => $user->id,
        'name' => 'Original Plan',
        'start_date' => '2023-01-01',
        'duration' => 7,
        'people_count' => 2
    ]);

    // Create days for the meal plan
    for ($i = 1; $i <= $mealPlan->duration; $i++) {
        $mealPlan->days()->create(['day_number' => $i]);
    }

    // Add a recipe to the meal plan
    $recipe = Recipe::factory()->create(['user_id' => $user->id]);
    $mealPlan->recipes()->attach($recipe->id, [
        'scale_factor' => 1.5,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Add a meal assignment
    $day = $mealPlan->days()->first();
    $mealPlanRecipe = $mealPlan->recipes()->first()->pivot;
    $assignment = MealAssignment::create([
        'meal_plan_day_id' => $day->id,
        'meal_plan_recipe_id' => $mealPlanRecipe->id,
        'servings' => 2,
        'to_cook' => true
    ]);

    // Set up copy data
    $copyData = [
        'name' => 'Copied Plan',
        'start_date' => '2023-01-15',
        'people_count' => 4
    ];

    // Submit request to copy the meal plan
    $response = $this->actingAs($user)
        ->post(route('meal-plans.copy', $mealPlan), $copyData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check that a new meal plan was created with the correct data
    $this->assertDatabaseHas('meal_plans', [
        'user_id' => $user->id,
        'name' => 'Copied Plan',
        'start_date' => '2023-01-15 00:00:00',
        'duration' => 7,
        'people_count' => 4
    ]);

    // Get the new meal plan
    $newMealPlan = MealPlan::where('name', 'Copied Plan')->first();
    $this->assertNotNull($newMealPlan);

    // Check that days were created
    $this->assertEquals($mealPlan->duration, $newMealPlan->days()->count());

    // Check that the recipe was copied
    $this->assertTrue($newMealPlan->recipes()->where('recipe_id', $recipe->id)->exists());
    $newPlanRecipe = $newMealPlan->recipes()->first();
    $this->assertEquals(1.5, $newPlanRecipe->pivot->scale_factor);

    // Check that meal assignments were copied correctly
    $newDay = $newMealPlan->days()->where('day_number', $day->day_number)->first();
    $newPlanRecipePivot = $newMealPlan->recipes()->first()->pivot;

    $this->assertTrue($newDay->mealAssignments()->exists());
    $newAssignment = $newDay->mealAssignments()->first();
    $this->assertEquals($newPlanRecipePivot->id, $newAssignment->meal_plan_recipe_id);
    $this->assertEquals(2, $newAssignment->servings);
    $this->assertTrue($newAssignment->to_cook);
});

test('a user cannot copy another users meal plan', function () {
    // Create two users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a meal plan for user1
    $mealPlan = MealPlan::factory()->create([
        'user_id' => $user1->id,
        'name' => 'Original Plan',
        'start_date' => '2023-01-01',
        'duration' => 7,
        'people_count' => 2
    ]);

    // Try to copy user1's meal plan as user2
    $response = $this->actingAs($user2)
        ->post(route('meal-plans.copy', $mealPlan), [
            'name' => 'Stolen Plan',
            'start_date' => '2023-01-15'
        ]);

    $response->assertForbidden();

    // Verify the plan wasn't copied
    $this->assertDatabaseMissing('meal_plans', [
        'user_id' => $user2->id,
        'name' => 'Stolen Plan'
    ]);
});

test('a copy includes default name if none provided', function () {
    $user = User::factory()->create();
    $mealPlan = MealPlan::factory()->create([
        'user_id' => $user->id,
        'name' => 'Original Plan',
        'start_date' => '2023-01-01',
        'duration' => 7,
        'people_count' => 2
    ]);

    // Create days for the meal plan
    for ($i = 1; $i <= $mealPlan->duration; $i++) {
        $mealPlan->days()->create(['day_number' => $i]);
    }

    // Copy without providing a name
    $response = $this->actingAs($user)
        ->post(route('meal-plans.copy', $mealPlan), [
            'start_date' => '2023-01-15'
        ]);

    $response->assertRedirect();

    // Check that a new meal plan was created with the default name
    $this->assertDatabaseHas('meal_plans', [
        'user_id' => $user->id,
        'name' => 'Copy of Original Plan',
        'start_date' => '2023-01-15 00:00:00'
    ]);
});
