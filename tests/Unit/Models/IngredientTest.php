<?php

declare(strict_types=1);

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can be added to a recipe with amount and unit', function () {
    $ingredient = Ingredient::factory()->create();
    $recipe = Recipe::factory()->create();

    $ingredient->recipes()->attach($recipe, [
        'amount' => 2.5,
        'unit' => 'cups',
    ]);

    expect($ingredient->recipes->contains($recipe))->toBeTrue();
    expect($ingredient->recipes->first()->pivot->amount)->toBe(2.5);
    expect($ingredient->recipes->first()->pivot->unit)->toBe('cups');
});

test('it can be marked as common', function () {
    $ingredient = Ingredient::factory()->common()->create();

    expect($ingredient->is_common)->toBeTrue();
});

test('it is not common by default', function () {
    $ingredient = Ingredient::factory()->create();

    expect($ingredient->is_common)->toBeFalse();
});
