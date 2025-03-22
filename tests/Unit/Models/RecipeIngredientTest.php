<?php

declare(strict_types=1);

use App\Enums\MeasurementUnit;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\ValueObjects\Measurement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('recipe ingredient belongs to a recipe', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    $recipeIngredient = new RecipeIngredient([
        'recipe_id' => $recipe->id,
        'ingredient_id' => $ingredient->id,
        'amount' => 2.5,
        'unit' => MeasurementUnit::CUP,
    ]);

    expect($recipeIngredient->recipe())->toBeInstanceOf(BelongsTo::class);
    expect($recipeIngredient->recipe)->toBeInstanceOf(Recipe::class);
    expect($recipeIngredient->recipe->id)->toBe($recipe->id);
});

test('recipe ingredient belongs to an ingredient', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    $recipeIngredient = new RecipeIngredient([
        'recipe_id' => $recipe->id,
        'ingredient_id' => $ingredient->id,
        'amount' => 2.5,
        'unit' => MeasurementUnit::CUP,
    ]);

    expect($recipeIngredient->ingredient())->toBeInstanceOf(BelongsTo::class);
    expect($recipeIngredient->ingredient)->toBeInstanceOf(Ingredient::class);
    expect($recipeIngredient->ingredient->id)->toBe($ingredient->id);
});

test('recipe ingredient can create measurement value object', function () {
    $recipeIngredient = new RecipeIngredient([
        'amount' => 2.5,
        'unit' => MeasurementUnit::CUP,
    ]);

    $measurement = $recipeIngredient->measurement();

    expect($measurement)->toBeInstanceOf(Measurement::class);
    expect($measurement->amount)->toBe(2.5);
    expect($measurement->unit)->toBe(MeasurementUnit::CUP);
});
