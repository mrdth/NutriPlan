<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealPlanRecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_recipe_to_meal_plan(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.5,
        ]);
    }

    public function test_user_cannot_add_recipe_to_meal_plan_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user1->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user2)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_meal_plan_id_is_required_when_adding_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertSessionHasErrors('meal_plan_id');
    }

    public function test_recipe_id_is_required_when_adding_recipe(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertSessionHasErrors('recipe_id');
    }

    public function test_scale_factor_must_be_numeric(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 'not-a-number',
        ]);

        $response->assertSessionHasErrors('scale_factor');
    }

    public function test_user_can_remove_recipe_from_meal_plan(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        // First add the recipe to the meal plan
        $meal_plan->recipes()->attach($recipe->id, ['scale_factor' => 1.0]);

        // Use our debug route instead
        $routeUrl = route('meal-plans.remove-recipe', ['id' => $meal_plan->id, 'recipeId' => $recipe->id]);

        $response = $this->actingAs($user)
                         ->from('/previous-page')
                         ->delete($routeUrl);

        $response->assertRedirect('/previous-page');
        $this->assertDatabaseMissing('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_user_cannot_remove_recipe_from_meal_plan_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user1->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user1->id]);

        // First add the recipe to the meal plan
        $meal_plan->recipes()->attach($recipe->id, ['scale_factor' => 1.0]);

        // Use our debug route instead
        $routeUrl = route('meal-plans.remove-recipe', ['id' => $meal_plan->id, 'recipeId' => $recipe->id]);

        $response = $this->actingAs($user2)->delete($routeUrl);

        $response->assertStatus(403);
        $this->assertDatabaseHas('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_add_recipe_to_meal_plan(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_remove_recipe_from_meal_plan(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create(['user_id' => $user->id]);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        // First add the recipe to the meal plan
        $meal_plan->recipes()->attach($recipe->id, ['scale_factor' => 1.0]);

        // Use our debug route instead
        $routeUrl = route('meal-plans.remove-recipe', ['id' => $meal_plan->id, 'recipeId' => $recipe->id]);

        $response = $this->delete($routeUrl);

        $response->assertRedirect(route('login'));
    }

    public function test_available_servings_are_calculated_when_adding_recipe(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create([
            'user_id' => $user->id,
            'people_count' => 2,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'servings' => 4,
        ]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'servings_available' => 2.0, // 4 servings / 2 people = 2 available servings
        ]);
    }

    public function test_available_servings_are_zero_when_plan_has_no_people(): void
    {
        $user = User::factory()->create();
        $meal_plan = MealPlan::factory()->create([
            'user_id' => $user->id,
            'people_count' => 0,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'servings' => 4,
        ]);

        $response = $this->actingAs($user)->post(route('meal-plans.add-recipe'), [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'scale_factor' => 1.0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meal_plan_recipe', [
            'meal_plan_id' => $meal_plan->id,
            'recipe_id' => $recipe->id,
            'servings_available' => 0.0,
        ]);
    }
}
