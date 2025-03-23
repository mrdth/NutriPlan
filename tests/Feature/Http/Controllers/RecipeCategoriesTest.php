<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->recipe = Recipe::factory()->for($this->user)->create();
    $this->categories = Category::factory()->count(5)->create();

    // Create ingredients for the recipe
    $this->ingredients = \App\Models\Ingredient::factory()->count(3)->create();
    $this->recipe->ingredients()->attach(
        $this->ingredients->mapWithKeys(fn ($ingredient) => [
            $ingredient->id => [
                'amount' => fake()->randomFloat(2, 0.25, 10),
                'unit' => fake()->randomElement(array_column(\App\Enums\MeasurementUnit::cases(), 'value')),
            ],
        ])->toArray()
    );
});

test('recipe edit page loads with all categories', function () {
    $response = actingAs($this->user)
        ->get(route('recipes.edit', $this->recipe));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
            ->component('Recipes/Edit')
            ->has('categories', $this->categories->count())
    );
});

test('recipe can be updated with selected categories', function () {
    // Select 3 categories
    $selectedCategories = $this->categories->take(3);

    $response = actingAs($this->user)
        ->put(route('recipes.update', $this->recipe), [
            'title' => $this->recipe->title,
            'description' => $this->recipe->description,
            'instructions' => $this->recipe->instructions,
            'prep_time' => $this->recipe->prep_time,
            'cooking_time' => $this->recipe->cooking_time,
            'servings' => $this->recipe->servings,
            'categories' => $selectedCategories->pluck('id')->toArray(),
            'ingredients' => $this->recipe->ingredients->map(fn ($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'amount' => $ingredient->pivot->amount,
                'unit' => $ingredient->pivot->unit->value ?? '',
            ])->toArray(),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->recipe->refresh();
    expect($this->recipe->categories)->toHaveCount(3);
    expect($this->recipe->categories->pluck('id')->toArray())
        ->toEqual($selectedCategories->pluck('id')->toArray());
});

test('recipe categories can be updated by adding and removing categories', function () {
    // Initially assign 2 categories
    $initialCategories = $this->categories->take(2);
    $this->recipe->categories()->sync($initialCategories->pluck('id')->toArray());

    // Update to 3 different categories
    $newCategories = $this->categories->skip(2)->take(3);

    $response = actingAs($this->user)
        ->put(route('recipes.update', $this->recipe), [
            'title' => $this->recipe->title,
            'description' => $this->recipe->description,
            'instructions' => $this->recipe->instructions,
            'prep_time' => $this->recipe->prep_time,
            'cooking_time' => $this->recipe->cooking_time,
            'servings' => $this->recipe->servings,
            'categories' => $newCategories->pluck('id')->toArray(),
            'ingredients' => $this->recipe->ingredients->map(fn ($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'amount' => $ingredient->pivot->amount,
                'unit' => $ingredient->pivot->unit->value ?? '',
            ])->toArray(),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->recipe->refresh();
    expect($this->recipe->categories)->toHaveCount(3);
    expect($this->recipe->categories->pluck('id')->toArray())
        ->toEqual($newCategories->pluck('id')->toArray());

    // Verify that the initial categories are no longer associated
    $initialCategoryIds = $initialCategories->pluck('id')->toArray();
    foreach ($initialCategoryIds as $categoryId) {
        expect($this->recipe->categories->pluck('id')->toArray())->not->toContain($categoryId);
    }
});

test('recipe can have all categories removed', function () {
    // Initially assign categories
    $this->recipe->categories()->sync($this->categories->pluck('id')->toArray());
    expect($this->recipe->categories)->toHaveCount(5);

    // Update with empty categories array
    $response = actingAs($this->user)
        ->put(route('recipes.update', $this->recipe), [
            'title' => $this->recipe->title,
            'description' => $this->recipe->description,
            'instructions' => $this->recipe->instructions,
            'prep_time' => $this->recipe->prep_time,
            'cooking_time' => $this->recipe->cooking_time,
            'servings' => $this->recipe->servings,
            'categories' => [],
            'ingredients' => $this->recipe->ingredients->map(fn ($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'amount' => $ingredient->pivot->amount,
                'unit' => $ingredient->pivot->unit->value ?? '',
            ])->toArray(),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->recipe->refresh();
    expect($this->recipe->categories)->toHaveCount(0);
});
