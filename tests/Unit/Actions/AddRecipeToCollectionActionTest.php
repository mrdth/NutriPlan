<?php

declare(strict_types=1);

use App\Actions\AddRecipeToCollectionAction;
use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it adds a recipe to a collection', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
    ]);

    $action = new AddRecipeToCollectionAction();
    $action->handle($collection, $recipe);

    expect(
        $collection->recipes()
            ->where('recipe_id', $recipe->id)
            ->exists()
    )->toBeTrue();
});

test('it does not add duplicate recipe to collection', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
    ]);

    // Add the recipe to the collection
    $collection->recipes()->attach($recipe->id);

    // Try to add it again
    $action = new AddRecipeToCollectionAction();
    $action->handle($collection, $recipe);

    // Check that the recipe is in the collection only once
    expect($collection->recipes)->toHaveCount(1)
        ->and($collection->recipes()->where('recipe_id', $recipe->id)->count())->toBe(1);
});
