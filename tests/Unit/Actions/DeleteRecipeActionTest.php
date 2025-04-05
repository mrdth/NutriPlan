<?php

declare(strict_types=1);

use App\Actions\DeleteRecipeAction;
use App\Models\Recipe;
use App\Models\NutritionInformation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it deletes recipe and related records', function () {
    // Arrange
    $recipe = Recipe::factory()->create();
    $nutritionInfo = NutritionInformation::factory()->create([
        'recipe_id' => $recipe->id,
    ]);

    // Act
    $action = new DeleteRecipeAction();
    $result = $action->execute($recipe);

    // Assert
    expect($result)->toBeTrue();

    $this->assertDatabaseMissing('recipes', [
        'id' => $recipe->id
    ]);
    $this->assertDatabaseMissing('nutrition_information', [
        'id' => $nutritionInfo->id
    ]);
});

test('it throws exception when recipe does not exist', function () {
    // Arrange
    $recipe = Recipe::factory()->make(); // Not persisted to database
    $recipe->id = 999; // Invalid ID

    // Act & Assert
    $action = new DeleteRecipeAction();

    expect(fn () => $action->execute($recipe))
        ->toThrow(ModelNotFoundException::class);
});
