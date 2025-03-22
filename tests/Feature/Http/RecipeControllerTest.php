<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Enums\MeasurementUnit;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_recipe_list(): void
    {
        $user = User::factory()->create();
        $recipes = Recipe::factory()
            ->count(3)
            ->for($user)
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

        $response = $this
            ->actingAs($user)
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
                ->has('status')
            )
        );
    }

    public function test_guest_cannot_view_recipe_list(): void
    {
        Recipe::factory()->count(3)->create();

        $response = $this->get(route('recipes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_recipe_list_is_paginated(): void
    {
        $user = User::factory()->create();
        Recipe::factory()
            ->count(15)
            ->for($user)
            ->create();

        $response = $this
            ->actingAs($user)
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
    }

    public function test_recipes_are_ordered_by_latest_first(): void
    {
        $user = User::factory()->create();
        $oldRecipe = Recipe::factory()
            ->for($user)
            ->create(['created_at' => now()->subDays(2)]);
        $newRecipe = Recipe::factory()
            ->for($user)
            ->create(['created_at' => now()]);

        $response = $this
            ->actingAs($user)
            ->get(route('recipes.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
            ->component('Recipes/Index')
            ->where('recipes.data.0.id', $newRecipe->id)
            ->where('recipes.data.1.id', $oldRecipe->id)
        );
    }

    public function test_recipe_list_includes_categories_and_user(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()
            ->for($user)
            ->has(Category::factory()->count(2))
            ->create();

        $response = $this
            ->actingAs($user)
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
    }

    public function test_guest_cannot_create_recipe(): void
    {
        $response = $this->get(route('recipes.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_recipe(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('recipes.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
            ->component('Recipes/Create')
        );
    }

    public function test_user_can_store_recipe(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory(2)->create();
        $ingredients = Ingredient::factory(3)->create();

        $response = $this
            ->actingAs($user)
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

        $this->assertDatabaseHas('recipes', [
            'title' => 'Test Recipe',
            'description' => 'Test Description',
            'instructions' => 'Test Instructions',
            'prep_time' => 30,
            'cooking_time' => 45,
            'servings' => 4,
            'user_id' => $user->id,
        ]);

        $recipe = Recipe::where('title', 'Test Recipe')->first();
        $this->assertNotNull($recipe);

        $this->assertEquals($categories->pluck('id')->toArray(), $recipe->categories->pluck('id')->toArray());
        $this->assertEquals($ingredients->pluck('id')->toArray(), $recipe->ingredients->pluck('id')->toArray());
    }

    public function test_guest_cannot_view_recipe(): void
    {
        $recipe = Recipe::factory()->create();

        $response = $this->get(route('recipes.show', $recipe));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()
            ->for($user)
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

        $response = $this
            ->actingAs($user)
            ->get(route('recipes.show', $recipe));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
            ->component('Recipes/Show')
            ->has(
                'recipe',
                fn (AssertableInertia $recipe) => $recipe
                ->has('id')
                ->has('title')
                ->has('description')
                ->has('instructions')
                ->has('prep_time')
                ->has('cooking_time')
                ->has('servings')
                ->has('url')
                ->has('author')
                ->has('images')
                ->has('slug')
                ->has('status')
                ->has('user')
                ->has('categories')
                ->has('ingredients')
            )
        );
    }

    public function test_guest_cannot_edit_recipe(): void
    {
        $recipe = Recipe::factory()->create();

        $response = $this->get(route('recipes.edit', $recipe));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_edit_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()
            ->for($user)
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

        $response = $this
            ->actingAs($user)
            ->get(route('recipes.edit', $recipe));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
            ->component('Recipes/Edit')
            ->has(
                'recipe',
                fn (AssertableInertia $recipe) => $recipe
                ->has('id')
                ->has('title')
                ->has('description')
                ->has('instructions')
                ->has('prep_time')
                ->has('cooking_time')
                ->has('servings')
                ->has('url')
                ->has('author')
                ->has('images')
                ->has('slug')
                ->has('status')
                ->has('categories')
                ->has('ingredients')
            )
        );
    }

    public function test_user_cannot_edit_others_recipe(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($otherUser)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('recipes.edit', $recipe));

        $response->assertForbidden();
    }

    public function test_user_can_update_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();
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

        $response = $this
            ->actingAs($user)
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

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Updated Recipe',
            'description' => 'Updated Description',
            'instructions' => 'Updated Instructions',
            'prep_time' => 45,
            'cooking_time' => 60,
            'servings' => 6,
        ]);

        $recipe->refresh();
        $this->assertEquals($categories->pluck('id')->toArray(), $recipe->categories->pluck('id')->toArray());
        $this->assertEquals($ingredients->pluck('id')->toArray(), $recipe->ingredients->pluck('id')->toArray());
    }

    public function test_user_cannot_update_others_recipe(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($otherUser)->create();

        $response = $this
            ->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => 'Updated Recipe',
                'description' => 'Updated Description',
                'instructions' => 'Updated Instructions',
                'prep_time' => 45,
                'cooking_time' => 60,
                'servings' => 6,
            ]);

        $response->assertForbidden();
    }

    public function test_guest_cannot_delete_recipe(): void
    {
        $recipe = Recipe::factory()->create();

        $response = $this->delete(route('recipes.destroy', $recipe));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_delete_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('recipes.destroy', $recipe));

        $response->assertRedirect(route('recipes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
    }

    public function test_user_cannot_delete_others_recipe(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($otherUser)->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('recipes.destroy', $recipe));

        $response->assertForbidden();
    }
}
