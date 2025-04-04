<?php

declare(strict_types=1);

use App\Models\Recipe;
use App\Models\User;

test('authenticated user can search recipes', function () {
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

    Recipe::factory()->create([
        'user_id' => $user->id,
        'title' => 'Chicken Curry',
        'description' => 'Spicy main dish',
        'is_public' => true,
    ]);

    // Search for 'cake' should return the two cake recipes
    $response = $this->actingAs($user)
        ->getJson('/api/recipes/search?query=cake');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', $recipe1->title)
        ->assertJsonPath('data.1.title', $recipe2->title);
});

test('search returns users private recipes', function () {
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

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', $privateRecipe->title);
});

test('search does not return other users private recipes', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create a private recipe for user1
    Recipe::factory()->create([
        'user_id' => $user1->id,
        'title' => 'Secret Recipe',
        'description' => 'Private recipe',
        'is_public' => false,
    ]);

    // User2 should not see user1's private recipe
    $response = $this->actingAs($user2)
        ->getJson('/api/recipes/search?query=secret');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

test('unauthenticated user cannot search recipes', function () {
    $response = $this->getJson('/api/recipes/search?query=cake');

    $response->assertStatus(401);
});

test('search with empty query returns empty array', function () {
    $user = User::factory()->create();

    // Create a recipe that won't be returned for empty query
    Recipe::factory()->create([
        'user_id' => $user->id,
        'title' => 'Chocolate Cake',
        'is_public' => true,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/api/recipes/search?query=');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});
