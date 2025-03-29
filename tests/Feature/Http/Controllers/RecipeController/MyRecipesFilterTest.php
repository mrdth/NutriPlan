<?php

declare(strict_types=1);

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('recipe index includes show_mine filter parameter', function () {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->get(route('recipes.index', ['show_mine' => true]));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->where('filter.show_mine', '1')
    );
});

test('user can filter recipes to view only their own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Create recipes owned by the current user
    $userRecipes = Recipe::factory()
        ->count(3)
        ->for($user)
        ->create();

    // Create recipes owned by another user
    $otherUserRecipes = Recipe::factory()
        ->count(2)
        ->for($otherUser)
        ->create();

    // Test without filter (should see all recipes)
    $response = actingAs($user)
        ->get(route('recipes.index'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data', 5)
    );

    // Test with show_mine filter (should only see user's recipes)
    $response = actingAs($user)
        ->get(route('recipes.index', ['show_mine' => true]));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data', 3)
        ->where('filter.show_mine', '1')
    );

    // Verify that only the user's recipes are returned
    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->where('recipes.data.0.user.id', $user->id)
        ->where('recipes.data.1.user.id', $user->id)
        ->where('recipes.data.2.user.id', $user->id)
    );
});
