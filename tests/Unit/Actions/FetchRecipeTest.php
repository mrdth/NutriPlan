<?php

use App\Actions\FetchRecipe;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->action = new FetchRecipe();
    $this->user = createUser();
    Auth::login($this->user);
});

it('fetches and parses a recipe from a URL', function () {
    Http::fake([
        'recipe.example.com' => Http::response(
            file_get_contents(__DIR__ . '/../../Fixtures/recipe-with-categories.html'),
            200
        ),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->categories)
        ->toHaveCount(3);

    $categoryNames = $recipe->categories->pluck('name')->all();
    expect($categoryNames)->toContain('Dinner', 'Italian', 'Quick Meals');
});

it('throws ConnectionFailedException when URL is unreachable', function () {
    Http::fake([
        'recipe.example.com' => Http::response(null, 404),
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(ConnectionFailedException::class);

it('throws NoStructuredDataException when no recipe data is found', function () {
    Http::fake([
        'recipe.example.com' => Http::response('<html><body>No recipe here</body></html>', 200),
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(NoStructuredDataException::class);

it('handles network timeouts gracefully', function () {
    Http::fake([
        'recipe.example.com' => Http::response(status: 408)
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(ConnectionFailedException::class);

it('handles invalid response content', function () {
    Http::fake([
        'recipe.example.com' => Http::response('Invalid content', 200)
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(NoStructuredDataException::class);

it('handles non-UTF8 encoded content', function () {
    $content = mb_convert_encoding(
        file_get_contents(__DIR__ . '/../../Fixtures/recipe-with-categories.html'),
        'ISO-8859-1',
        'UTF-8'
    );

    Http::fake([
        'recipe.example.com' => Http::response($content, 200),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->title)
        ->not->toBeEmpty();
});

it('tries JSON-LD parser first', function () {
    Http::fake([
        'recipe.example.com' => Http::response(
            file_get_contents(__DIR__ . '/../../Fixtures/recipe-json-ld.html'),
            200
        ),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->title)
        ->toBe('JSON-LD Recipe')
        ->and($recipe->prep_time)
        ->toBe(15)
        ->and($recipe->cooking_time)
        ->toBe(30)
        ->and($recipe->servings)
        ->toBe(4);
});

it('falls back to Microdata parser if JSON-LD fails', function () {
    Http::fake([
        'recipe.example.com' => Http::response(
            file_get_contents(__DIR__ . '/../../Fixtures/recipe-microdata.html'),
            200
        ),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->title)
        ->toBe('Microdata Recipe')
        ->and($recipe->prep_time)
        ->toBe(15)
        ->and($recipe->cooking_time)
        ->toBe(30)
        ->and($recipe->servings)
        ->toBe(4);
});

it('falls back to RDFa parser if other parsers fail', function () {
    Http::fake([
        'recipe.example.com' => Http::response(
            file_get_contents(__DIR__ . '/../../Fixtures/recipe-rdfa.html'),
            200
        ),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->title)
        ->toBe('RDFa Recipe')
        ->and($recipe->prep_time)
        ->toBe(15)
        ->and($recipe->cooking_time)
        ->toBe(30)
        ->and($recipe->servings)
        ->toBe(4);
});

it('handles empty response content', function () {
    Http::fake([
        'recipe.example.com' => Http::response('', 200),
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(NoStructuredDataException::class);

it('handles malformed HTML content', function () {
    Http::fake([
        'recipe.example.com' => Http::response('<html><body><div>Incomplete HTML', 200),
    ]);

    $this->action->handle('https://recipe.example.com');
})->throws(NoStructuredDataException::class);

it('handles malformed URLs', function () {
    $this->action->handle('not-a-url');
})->throws(ConnectionFailedException::class);

it('handles recipe with missing required fields', function () {
    $content = <<<HTML
<html>
<body>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Recipe",
    "name": "",
    "recipeInstructions": []
}
</script>
</body>
</html>
HTML;

    Http::fake([
        'recipe.example.com' => Http::response($content, 200),
    ]);

    $recipe = $this->action->handle('https://recipe.example.com');

    expect($recipe)
        ->toBeInstanceOf(Recipe::class)
        ->and($recipe->title)
        ->toBe('')
        ->and($recipe->instructions)
        ->toBe('');
});
