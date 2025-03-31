<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_meal_plans_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('meal-plans.index'));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_meal_plans_page(): void
    {
        $response = $this->get(route('meal-plans.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_meal_plan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('meal-plans.store'), [
            'name' => 'Test Meal Plan',
            'start_date' => now()->format('Y-m-d'),
            'duration' => 7,
            'people_count' => 4,
        ]);

        $response->assertRedirect(route('meal-plans.index'));
        $this->assertDatabaseHas('meal_plans', [
            'user_id' => $user->id,
            'name' => 'Test Meal Plan',
            'duration' => 7,
            'people_count' => 4,
        ]);
    }

    public function test_user_can_view_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('meal-plans.show', $mealPlan));

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_meal_plan_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $mealPlan = MealPlan::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get(route('meal-plans.show', $mealPlan));

        $response->assertStatus(403);
    }

    public function test_user_can_delete_meal_plan(): void
    {
        $user = User::factory()->create();
        $mealPlan = MealPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('meal-plans.destroy', $mealPlan));

        $response->assertRedirect(route('meal-plans.index'));
        $this->assertDatabaseMissing('meal_plans', [
            'id' => $mealPlan->id,
        ]);
    }

    public function test_user_cannot_delete_meal_plan_of_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $mealPlan = MealPlan::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete(route('meal-plans.destroy', $mealPlan));

        $response->assertStatus(403);
        $this->assertDatabaseHas('meal_plans', [
            'id' => $mealPlan->id,
        ]);
    }
}
