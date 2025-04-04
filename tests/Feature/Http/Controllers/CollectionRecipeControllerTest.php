<?php

declare(strict_types=1);

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;

test('store adds a recipe to collection', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('collections.add-recipe'), [
            'collection_id' => $collection->id,
            'recipe_id' => $recipe->id,
        ]);

    $response->assertSessionHas('success');

    expect(
        $collection->recipes()
            ->where('recipe_id', $recipe->id)
            ->exists()
    )->toBeTrue();
});

test('destroy removes a recipe from collection', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
    ]);

    $collection->recipes()->attach($recipe->id);

    $response = $this->actingAs($user)
        ->delete(route('collections.remove-recipe', [
            'collection' => $collection,
            'recipe' => $recipe,
        ]));

    $response->assertSessionHas('success');

    expect(
        $collection->recipes()
            ->where('recipe_id', $recipe->id)
            ->exists()
    )->toBeFalse();
});

test('user cannot add recipe to others collections', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $otherUser->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('collections.add-recipe'), [
            'collection_id' => $collection->id,
            'recipe_id' => $recipe->id,
        ]);

    $response->assertForbidden();
});

test('user cannot remove recipe from others collections', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $otherUser->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $collection->recipes()->attach($recipe->id);

    $response = $this->actingAs($user)
        ->delete(route('collections.remove-recipe', [
            'collection' => $collection,
            'recipe' => $recipe,
        ]));

    $response->assertForbidden();
});
