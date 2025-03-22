<?php

declare(strict_types=1);

use App\Enums\MeasurementUnit;
use App\Enums\RecipeStatus;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use App\ValueObjects\Measurement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it belongs to a user', function () {
    $recipe = Recipe::factory()->create();

    expect($recipe->user)->toBeInstanceOf(User::class);
});

test('it has many categories', function () {
    $recipe = Recipe::factory()
        ->has(Category::factory()->count(2))
        ->create();

    expect($recipe->categories)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->first()->toBeInstanceOf(Category::class);
});

test('it has many ingredients', function () {
    $recipe = Recipe::factory()->create();
    $ingredients = Ingredient::factory()->count(3)->create();

    $recipe->ingredients()->attach(
        $ingredients->mapWithKeys(fn ($ingredient) => [
            $ingredient->id => [
                'amount' => fake()->randomFloat(2, 0.25, 10),
                'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
            ],
        ])->toArray()
    );

    expect($recipe->ingredients)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(3)
        ->first()->toBeInstanceOf(Ingredient::class);
});

test('it can be published', function () {
    $recipe = Recipe::factory()->published()->create();

    expect($recipe->published_at)->not->toBeNull()
        ->and($recipe->status)->toBe(RecipeStatus::PUBLISHED);
});

test('it can be a draft', function () {
    $recipe = Recipe::factory()->draft()->create();

    expect($recipe->published_at)->toBeNull()
        ->and($recipe->status)->toBe(RecipeStatus::DRAFT);
});

test('it can get measurement for ingredient', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    $recipe->ingredients()->attach($ingredient, [
        'amount' => 2.5,
        'unit' => MeasurementUnit::CUP->value,
    ]);

    $measurement = $recipe->getMeasurementForIngredient($ingredient);

    expect($measurement)
        ->toBeInstanceOf(Measurement::class)
        ->and($measurement->amount)->toBe(2.5)
        ->and($measurement->unit)->toBe(MeasurementUnit::CUP);
});

test('it returns null measurement for non existent ingredient', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    expect($recipe->getMeasurementForIngredient($ingredient))->toBeNull();
});
