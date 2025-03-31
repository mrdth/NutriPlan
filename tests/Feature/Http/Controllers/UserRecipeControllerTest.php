<?php

declare(strict_types=1);

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('user can view recipes for a specific user', function () {
    // Arrange - Create users and recipes
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);

    // Create public recipes for user1
    $publicRecipes = Recipe::factory(3)->create([
        'user_id' => $user1->id,
        'is_public' => true,
    ]);

    // Create private recipes for user1
    $privateRecipes = Recipe::factory(2)->create([
        'user_id' => $user1->id,
        'is_public' => false,
    ]);

    // Create some recipes for user2 (to ensure they don't show up in user1's list)
    Recipe::factory(2)->create([
        'user_id' => $user2->id,
        'is_public' => true,
    ]);

    // Act - Visit user1's recipes page as an authenticated user2
    $response = $this->actingAs($user2)
        ->get(route('recipes.by-user', $user1->slug));

    // Assert - Only public recipes from user1 should be visible to user2
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Recipes/UserRecipes')
        ->has('recipes.data', count($publicRecipes))
        ->has('user.id')
        ->where('user.name', $user1->name)
        ->where('user.slug', $user1->slug)
        ->where('isOwner', false)
    );

    // Loop through and check that only public recipes are included
    $response->assertInertia(function (Assert $page) use ($publicRecipes) {
        $recipeIds = collect($page->toArray()['props']['recipes']['data'])
            ->pluck('id')
            ->toArray();

        foreach ($publicRecipes as $recipe) {
            expect($recipeIds)->toContain($recipe->id);
        }

        return $page;
    });
});

test('user can view all their own recipes including private ones', function () {
    // Arrange - Create a user with public and private recipes
    $user = User::factory()->create();

    // Create public recipes for the user
    $publicRecipes = Recipe::factory(2)->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    // Create private recipes for the user
    $privateRecipes = Recipe::factory(3)->create([
        'user_id' => $user->id,
        'is_public' => false,
    ]);

    // Act - Visit their own recipes page
    $response = $this->actingAs($user)
        ->get(route('recipes.by-user', $user->slug));

    // Assert - Both public and private recipes should be visible
    $totalRecipes = count($publicRecipes) + count($privateRecipes);

    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Recipes/UserRecipes')
        ->has('recipes.data', $totalRecipes)
        ->has('user.id')
        ->where('user.name', $user->name)
        ->where('user.slug', $user->slug)
        ->where('isOwner', true)
    );
});

test('user can filter recipes by category within a user profile', function () {
    // Arrange - Create user and recipes with categories
    $user = User::factory()->create();
    $category = \App\Models\Category::factory()->create();

    // Create recipes with the category
    $recipesWithCategory = Recipe::factory(2)->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    foreach ($recipesWithCategory as $recipe) {
        DB::table('category_recipe')->insert([
            'category_id' => $category->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    // Create recipes without the category
    Recipe::factory(3)->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    // Act - Visit the user's recipes page with category filter
    $response = $this->actingAs($user)
        ->get(route('recipes.by-user', [
            'user' => $user->slug,
            'category' => $category->id,
        ]));

    // Assert - Only recipes with the specified category should be visible
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Recipes/UserRecipes')
        ->has('recipes.data', count($recipesWithCategory))
        ->where('filter.category', (string) $category->id)
    );
});

test('empty state is displayed when user has no recipes', function () {
    // Arrange - Create two users, one with no recipes
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // User1 has no recipes
    // Create some recipes for user2
    Recipe::factory(3)->create([
        'user_id' => $user2->id,
        'is_public' => true,
    ]);

    // Act - Visit user1's recipes page
    $response = $this->actingAs($user2)
        ->get(route('recipes.by-user', $user1->slug));

    // Assert - Page loads with empty recipes collection
    $response->assertStatus(200);
    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Recipes/UserRecipes')
        ->has('recipes.data', 0)
        ->where('user.slug', $user1->slug)
    );
});

test('user profile page links are generated correctly from recipe cards', function () {
    // Arrange - Create a user with a public recipe
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    // Act - Load the main recipes index
    $response = $this->actingAs($user)
        ->get(route('recipes.index'));

    // Assert - Recipes data contains the user slug
    $response->assertStatus(200);
    $response->assertInertia(function (Assert $page) use ($user) {
        $recipesData = collect($page->toArray()['props']['recipes']['data']);
        $firstRecipe = $recipesData->first();

        // Check that user data with slug is included in the recipe
        expect($firstRecipe['user'])->toHaveKey('slug');
        expect($firstRecipe['user']['slug'])->toBe($user->slug);

        return $page;
    });
});
