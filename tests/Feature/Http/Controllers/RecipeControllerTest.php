<?php

declare(strict_types=1);

use App\Enums\MeasurementUnit;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can view recipe list', function () {
    $recipes = Recipe::factory()
        ->count(3)
        ->for($this->user)
        ->has(Category::factory()->count(2))
        ->create()
        ->each(function (Recipe $recipe) {
            $recipe->ingredients()->attach(
                Ingredient::factory()
                    ->count(3)
                    ->create()
                    ->mapWithKeys(fn ($ingredient) => [
                        $ingredient->id => [
                            'amount' => fake()->randomFloat(2, 0.25, 10),
                            'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                        ],
                    ])
                    ->toArray()
            );
        });

    $response = actingAs($this->user)
        ->get(route('recipes.index'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data', 3)
        ->has(
            'recipes.data.0',
            fn (AssertableInertia $page) => $page
            ->has('id')
            ->has('title')
            ->has('description')
            ->has('prep_time')
            ->has('cooking_time')
            ->has('servings')
            ->has('images')
            ->has('categories')
            ->has('user')
            ->has('instructions')
            ->has('url')
            ->has('author')
            ->has('slug')
            ->has('created_at')
            ->has('is_favorited')
            ->has('is_public')
        )
    );
});

test('guest cannot view recipe list', function () {
    Recipe::factory()->count(3)->create();

    $response = get(route('recipes.index'));

    $response->assertRedirect(route('login'));
});

test('recipe list is paginated', function () {
    Recipe::factory()
        ->count(15)
        ->for($this->user)
        ->create();

    $response = actingAs($this->user)
        ->get(route('recipes.index'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data', 12) // Default pagination is 12 items
        ->has('recipes.links')
        ->has('recipes.current_page')
        ->has('recipes.next_page_url')
        ->has('recipes.path')
        ->has('recipes.per_page')
        ->has('recipes.prev_page_url')
        ->has('recipes.to')
        ->has('recipes.total')
    );
});

test('recipes are ordered by latest first', function () {
    $oldRecipe = Recipe::factory()
        ->for($this->user)
        ->create(['created_at' => now()->subDays(2)]);
    $newRecipe = Recipe::factory()
        ->for($this->user)
        ->create(['created_at' => now()]);

    $response = actingAs($this->user)
        ->get(route('recipes.index'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->where('recipes.data.0.id', $newRecipe->id)
        ->where('recipes.data.1.id', $oldRecipe->id)
    );
});

test('recipe list includes categories and user', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->has(Category::factory()->count(2))
        ->create();

    $response = actingAs($this->user)
        ->get(route('recipes.index'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Index')
        ->has('recipes.data.0.categories', 2)
        ->has(
            'recipes.data.0.user',
            fn (AssertableInertia $page) => $page
            ->has('id')
            ->has('name')
        )
    );
});

test('guest cannot create recipe', function () {
    $response = get(route('recipes.create'));

    $response->assertRedirect(route('login'));
});

test('user can create recipe', function () {
    $response = actingAs($this->user)
        ->get(route('recipes.create'));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Create')
    );
});

test('user can store recipe', function () {
    $categories = Category::factory(2)->create();
    $ingredients = Ingredient::factory(3)->create();

    $response = actingAs($this->user)
        ->post(route('recipes.store'), [
            'title' => 'Test Recipe',
            'description' => 'Test Description',
            'instructions' => 'Test Instructions',
            'prep_time' => 30,
            'cooking_time' => 45,
            'servings' => 4,
            'categories' => $categories->pluck('id')->toArray(),
            'ingredients' => $ingredients->map(fn ($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'amount' => 2.5,
                'unit' => MeasurementUnit::CUP->value,
            ])->toArray(),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Recipe::where('title', 'Test Recipe')->exists())->toBeTrue();

    $recipe = Recipe::where('title', 'Test Recipe')->first();
    expect($recipe->categories)->toHaveCount(2);
    expect($recipe->ingredients)->toHaveCount(3);
});

test('guest cannot view recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = get(route('recipes.show', $recipe));

    $response->assertRedirect(route('login'));
});

test('user can view recipe', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->has(Category::factory()->count(2))
        ->create();

    $recipe->ingredients()->attach(
        Ingredient::factory()
            ->count(3)
            ->create()
            ->mapWithKeys(fn ($ingredient) => [
                $ingredient->id => [
                    'amount' => fake()->randomFloat(2, 0.25, 10),
                    'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                ],
            ])
            ->toArray()
    );

    $response = actingAs($this->user)
        ->get(route('recipes.show', $recipe));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Show')
        ->has('recipe')
        ->where('recipe.id', $recipe->id)
        ->has('recipe.categories', 2)
        ->has('recipe.ingredients', 3)
    );
});

test('guest cannot edit recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = get(route('recipes.edit', $recipe));

    $response->assertRedirect(route('login'));
});

test('user can edit own recipe', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->has(Category::factory()->count(2))
        ->create();

    $recipe->ingredients()->attach(
        Ingredient::factory()
            ->count(3)
            ->create()
            ->mapWithKeys(fn ($ingredient) => [
                $ingredient->id => [
                    'amount' => fake()->randomFloat(2, 0.25, 10),
                    'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                ],
            ])
            ->toArray()
    );

    $response = actingAs($this->user)
        ->get(route('recipes.edit', $recipe));

    $response->assertInertia(
        fn (AssertableInertia $page) => $page
        ->component('Recipes/Edit')
        ->has('recipe')
        ->where('recipe.id', $recipe->id)
        ->has('recipe.categories', 2)
        ->has('recipe.ingredients', 3)
    );
});

test('user cannot edit others recipe', function () {
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($otherUser)
        ->create();

    $response = actingAs($this->user)
        ->get(route('recipes.edit', $recipe));

    $response->assertForbidden();
});

test('user can update own recipe', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->create();

    $categories = Category::factory(2)->create();

    $recipe->ingredients()->attach(
        Ingredient::factory()
            ->count(3)
            ->create()
            ->mapWithKeys(fn ($ingredient) => [
                $ingredient->id => [
                    'amount' => fake()->randomFloat(2, 0.25, 10),
                    'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                ],
            ])
            ->toArray()
    );

    $response = actingAs($this->user)
        ->put(route('recipes.update', $recipe), [
            'title' => 'Updated Recipe',
            'description' => 'Updated Description',
            'instructions' => 'Updated Instructions',
            'prep_time' => 45,
            'cooking_time' => 60,
            'servings' => 6,
            'categories' => $categories->pluck('id')->toArray(),
            'ingredients' => $recipe->ingredients->map(fn ($ingredient) => [
                'ingredient_id' => $ingredient->id,
                'amount' => 3.5,
                'unit' => MeasurementUnit::TABLESPOON->value,
            ])->toArray(),
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $recipe->refresh();
    expect($recipe->title)->toBe('Updated Recipe');
    expect($recipe->description)->toBe('Updated Description');
    expect($recipe->instructions)->toBe('Updated Instructions');
    expect($recipe->prep_time)->toBe(45);
    expect($recipe->cooking_time)->toBe(60);
    expect($recipe->servings)->toBe(6);
    expect($recipe->categories)->toHaveCount(2);
    expect($recipe->ingredients)->toHaveCount(3);
});

test('user cannot update others recipe', function () {
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($otherUser)
        ->create();

    $response = actingAs($this->user)
        ->put(route('recipes.update', $recipe), [
            'title' => 'Updated Recipe',
            'description' => 'Updated Description',
            'instructions' => 'Updated Instructions',
            'prep_time' => 45,
            'cooking_time' => 60,
            'servings' => 6,
        ]);

    $response->assertForbidden();
});

test('guest cannot delete recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = delete(route('recipes.destroy', $recipe));

    $response->assertRedirect(route('login'));
});

test('user can delete own recipe', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->create();

    $response = actingAs($this->user)
        ->delete(route('recipes.destroy', $recipe));

    $response->assertRedirect(route('recipes.index'));
    $response->assertSessionHas('success');

    expect(Recipe::find($recipe->id))->toBeNull();
});

test('user cannot delete others recipe', function () {
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($otherUser)
        ->create();

    $response = actingAs($this->user)
        ->delete(route('recipes.destroy', $recipe));

    $response->assertForbidden();
});

test('user can delete own recipe with nutrition information', function () {
    $recipe = Recipe::factory()
        ->for($this->user)
        ->create();

    $nutrition = $recipe->nutritionInformation()->create([
        'calories' => '200 kcal',
        'protein_content' => '10g',
        'carbohydrate_content' => '30g',
        'fat_content' => '5g',
    ]);

    $response = actingAs($this->user)
        ->delete(route('recipes.destroy', $recipe));

    $response->assertRedirect(route('recipes.index'));
    $response->assertSessionHas('success');

    expect(Recipe::find($recipe->id))->toBeNull();
    expect(\App\Models\NutritionInformation::find($nutrition->id))->toBeNull();
});
