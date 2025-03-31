<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

test('categories index page displays categories with recipe counts', function () {
    // Create a user
    $user = User::factory()->create();

    // Create categories with recipes
    $categories = Category::factory()->count(3)->create();
    $recipes = Recipe::factory()->count(5)->create([
        'user_id' => $user->id,
        'is_public' => true
    ]);

    // Associate recipes with categories
    foreach ($recipes as $recipe) {
        $recipe->categories()->attach($categories->random(2));
    }

    // Act: Visit the categories page
    $response = $this
        ->actingAs($user)
        ->get(route('categories.index'));

    // Assert: Page loads successfully with categories data
    $response->assertStatus(200);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Categories/Index')
        ->has('categories', $categories->count())
        ->where('categories.0.recipes_count', fn ($count) => $count >= 0)
    );
});

test('category show page displays recipes filtered by category', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a category and recipes
    $category = Category::factory()->create();
    $categoryRecipes = Recipe::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_public' => true
    ]);
    $otherRecipes = Recipe::factory()->count(2)->create([
        'user_id' => $user->id,
        'is_public' => true
    ]);

    // Associate some recipes with the category
    foreach ($categoryRecipes as $recipe) {
        $recipe->categories()->attach($category);
    }

    // Act: Visit the category show page
    $response = $this
        ->actingAs($user)
        ->get(route('categories.show', $category));

    // Assert: Page loads successfully with filtered recipes
    $response->assertStatus(200);
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data', $categoryRecipes->count())
        ->has('category')
        ->where('category.id', $category->id)
    );
});
