<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_search_recipes(): void
    {
        $user = User::factory()->create();

        // Create some test recipes
        $recipe1 = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Chocolate Cake',
            'description' => 'Delicious dessert',
            'is_public' => true,
        ]);

        $recipe2 = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Vanilla Cake',
            'description' => 'Another delicious dessert',
            'is_public' => true,
        ]);

        $recipe3 = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Chicken Curry',
            'description' => 'Spicy main dish',
            'is_public' => true,
        ]);

        // Search for 'cake' should return the two cake recipes
        $response = $this->actingAs($user)
            ->getJson('/api/recipes/search?query=cake');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.title', $recipe1->title);
        $response->assertJsonPath('data.1.title', $recipe2->title);
    }

    public function test_search_returns_users_private_recipes(): void
    {
        $user = User::factory()->create();

        // Create a private recipe for the user
        $privateRecipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Secret Recipe',
            'description' => 'My private recipe',
            'is_public' => false,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/recipes/search?query=secret');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', $privateRecipe->title);
    }

    public function test_search_does_not_return_other_users_private_recipes(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a private recipe for user1
        $privateRecipe = Recipe::factory()->create([
            'user_id' => $user1->id,
            'title' => 'Secret Recipe',
            'description' => 'Private recipe',
            'is_public' => false,
        ]);

        // User2 should not see user1's private recipe
        $response = $this->actingAs($user2)
            ->getJson('/api/recipes/search?query=secret');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_unauthenticated_user_cannot_search_recipes(): void
    {
        $response = $this->getJson('/api/recipes/search?query=cake');

        $response->assertStatus(401);
    }

    public function test_search_with_empty_query_returns_empty_array(): void
    {
        $user = User::factory()->create();

        // Create a recipe that won't be returned for empty query
        Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Chocolate Cake',
            'is_public' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/recipes/search?query=');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }
}
