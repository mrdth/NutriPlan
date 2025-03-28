<?php

declare(strict_types=1);

use App\Actions\FetchRecipe;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->fetchRecipeMock = \Mockery::mock(FetchRecipe::class);
    $this->app->instance(FetchRecipe::class, $this->fetchRecipeMock);
});

test('it can reimport a specific recipe by ID', function () {
    // Create a recipe with a URL
    $recipe = Recipe::factory()->create([
        'url' => 'https://example.com/recipe',
        'title' => 'Test Recipe',
    ]);

    // Mock the FetchRecipe action to return the recipe
    $this->fetchRecipeMock
        ->shouldReceive('handle')
        ->once()
        ->with($recipe->url)
        ->andReturn($recipe);

    // Run the command with the recipe ID
    $this->artisan('recipes:reimport', ['--id' => $recipe->id])
        ->expectsOutput("Recipe '{$recipe->title}' reimported successfully.")
        ->assertSuccessful();
});

test('it shows error when recipe ID is not found', function () {
    // Non-existent recipe ID
    $nonExistentId = 999;

    // Run the command with a non-existent recipe ID
    $this->artisan('recipes:reimport', ['--id' => $nonExistentId])
        ->expectsOutput("Recipe with ID {$nonExistentId} not found.")
        ->assertFailed();
});

test('it can reimport all recipes with URLs', function () {
    // Create multiple recipes with URLs
    $recipes = Recipe::factory()->count(3)->create([
        'url' => fn () => 'https://example.com/recipe/' . fake()->uuid,
    ]);

    // Mock the FetchRecipe action to return each recipe
    foreach ($recipes as $recipe) {
        $this->fetchRecipeMock
            ->shouldReceive('handle')
            ->once()
            ->with($recipe->url)
            ->andReturn($recipe);
    }

    // Run the command without any options to reimport all recipes
    $this->artisan('recipes:reimport')
        ->expectsOutput('Recipe reimport completed.')
        ->assertSuccessful();
});

test('it handles recipes without URLs', function () {
    // Create a recipe without a URL
    $recipe = Recipe::factory()->create([
        'url' => null,
        'title' => 'Recipe Without URL',
    ]);

    // Create another recipe with a URL
    $recipeWithUrl = Recipe::factory()->create([
        'url' => 'https://example.com/recipe',
        'title' => 'Recipe With URL',
    ]);

    // Mock the FetchRecipe action to return the recipe with URL
    $this->fetchRecipeMock
        ->shouldReceive('handle')
        ->once()
        ->with($recipeWithUrl->url)
        ->andReturn($recipeWithUrl);

    // Run the command
    $this->artisan('recipes:reimport')
        ->expectsOutput('Recipe reimport completed.')
        ->assertSuccessful();
});

test('it handles no recipes with URLs', function () {
    // Create recipes without URLs
    Recipe::factory()->count(2)->create(['url' => null]);

    // Run the command
    $this->artisan('recipes:reimport')
        ->expectsOutput('No recipes with URLs found to reimport.')
        ->assertSuccessful();
});

test('it handles connection failures during reimport', function () {
    // Create a recipe with a URL
    $recipe = Recipe::factory()->create([
        'url' => 'https://example.com/recipe',
        'title' => 'Test Recipe',
    ]);

    // Mock the FetchRecipe action to throw a ConnectionFailedException
    $this->fetchRecipeMock
        ->shouldReceive('handle')
        ->once()
        ->with($recipe->url)
        ->andThrow(new ConnectionFailedException($recipe->url, 'Connection failed'));

    // Run the command with the recipe ID
    $this->artisan('recipes:reimport', ['--id' => $recipe->id])
        ->expectsOutput("Connection failed for recipe '{$recipe->title}' at URL: {$recipe->url}")
        ->assertSuccessful();
});

test('it handles no structured data during reimport', function () {
    // Create a recipe with a URL
    $recipe = Recipe::factory()->create([
        'url' => 'https://example.com/recipe',
        'title' => 'Test Recipe',
    ]);

    // Mock the FetchRecipe action to throw a NoStructuredDataException
    $this->fetchRecipeMock
        ->shouldReceive('handle')
        ->once()
        ->with($recipe->url)
        ->andThrow(new NoStructuredDataException($recipe->url));

    // Run the command with the recipe ID
    $this->artisan('recipes:reimport', ['--id' => $recipe->id])
        ->expectsOutput("No structured data found for recipe '{$recipe->title}' at URL: {$recipe->url}")
        ->assertSuccessful();
});

test('it handles unexpected exceptions during reimport', function () {
    // Create a recipe with a URL
    $recipe = Recipe::factory()->create([
        'url' => 'https://example.com/recipe',
        'title' => 'Test Recipe',
    ]);

    // Mock the FetchRecipe action to throw a generic Exception
    $this->fetchRecipeMock
        ->shouldReceive('handle')
        ->once()
        ->with($recipe->url)
        ->andThrow(new \Exception('Unexpected error'));

    // Run the command with the recipe ID
    $this->artisan('recipes:reimport', ['--id' => $recipe->id])
        ->expectsOutput("Error reimporting recipe '{$recipe->title}': Unexpected error")
        ->assertSuccessful();
});
