<?php

declare(strict_types=1);

use App\Actions\FetchRecipe;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = createUser();
    Auth::login($this->user);
});

it('imports a recipe from a URL', function () {
    $recipe = Recipe::factory()->create([
        'title' => 'Test Recipe',
        'url' => 'https://example.com/recipe',
    ]);

    $this->mock(FetchRecipe::class)
        ->shouldReceive('handle')
        ->once()
        ->with('https://example.com/recipe')
        ->andReturn($recipe);

    $response = $this->post(route('recipes.import'), [
        'url' => 'https://example.com/recipe',
    ]);

    $response->assertRedirect(route('recipes.show', ['recipe' => $recipe]))
        ->assertSessionHas('success', 'Recipe imported successfully. Please review and make any necessary adjustments.');

    $this->assertDatabaseHas('recipes', [
        'title' => 'Test Recipe',
        'url' => 'https://example.com/recipe',
    ]);
});

it('imports a recipe with categories', function () {
    $dinner = Category::factory()->create(['name' => 'Dinner']);
    $italian = Category::factory()->create(['name' => 'Italian']);

    $recipe = Recipe::factory()->create([
        'title' => 'Test Recipe with Categories',
        'url' => 'https://example.com/recipe',
    ]);

    $recipe->categories()->attach([$dinner->id, $italian->id]);

    $this->mock(FetchRecipe::class)
        ->shouldReceive('handle')
        ->once()
        ->with('https://example.com/recipe')
        ->andReturn($recipe);

    $response = $this->post(route('recipes.import'), [
        'url' => 'https://example.com/recipe',
    ]);

    $response->assertRedirect(route('recipes.show', ['recipe' => $recipe]));

    expect($recipe->fresh()->categories)->toHaveCount(2)
        ->and($recipe->fresh()->categories->pluck('name'))->toContain('Dinner', 'Italian');
});

it('handles connection failures gracefully', function () {
    $this->mock(FetchRecipe::class)
        ->shouldReceive('handle')
        ->once()
        ->with('https://example.com/recipe')
        ->andThrow(new ConnectionFailedException('https://example.com/recipe', 'Failed to connect'));

    $response = $this->post(route('recipes.import'), [
        'url' => 'https://example.com/recipe',
    ]);

    $response->assertSessionHasErrors([
        'url' => 'Could not connect to the recipe website. Please check the URL and try again.',
    ]);

    $this->assertDatabaseMissing('recipes', [
        'url' => 'https://example.com/recipe',
    ]);
});

it('handles missing recipe data gracefully', function () {
    $this->mock(FetchRecipe::class)
        ->shouldReceive('handle')
        ->once()
        ->with('https://example.com/recipe')
        ->andThrow(new NoStructuredDataException('https://example.com/recipe'));

    $response = $this->post(route('recipes.import'), [
        'url' => 'https://example.com/recipe',
    ]);

    $response->assertSessionHasErrors([
        'url' => 'We could not find any recipe data on this page. The website may not use standard recipe markup.',
    ]);

    $this->assertDatabaseMissing('recipes', [
        'url' => 'https://example.com/recipe',
    ]);
});

it('requires authentication', function () {
    Auth::logout();

    $response = $this->post(route('recipes.import'), [
        'url' => 'https://example.com/recipe',
    ]);

    $response->assertRedirect(route('login'));
});

it('validates the recipe URL', function () {
    $response = $this->post(route('recipes.import'), [
        'url' => 'not-a-url',
    ]);

    $response->assertSessionHasErrors(['url']);
});
