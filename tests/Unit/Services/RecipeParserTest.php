<?php

use App\Models\Category;
use App\Services\RecipeParser;
use Brick\StructuredData\Item;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->parser = new RecipeParser();
});

it('parses a single category from keywords', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['Dinner'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(1)
        ->first()->name->toBe('Dinner');
});

it('parses multiple categories from comma-separated keywords', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['Dinner, Healthy, Quick Meals'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(3)
        ->sequence(
            fn ($category) => $category->name->toBe('Dinner'),
            fn ($category) => $category->name->toBe('Healthy'),
            fn ($category) => $category->name->toBe('Quick Meals'),
        );
});

it('parses categories from recipeCategory', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'recipeCategory' => ['Main Course, Italian'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(2)
        ->sequence(
            fn ($category) => $category->name->toBe('Main Course'),
            fn ($category) => $category->name->toBe('Italian'),
        );
});

it('combines categories from keywords and recipeCategory', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['Dinner, Healthy'],
            'recipeCategory' => ['Main Course, Italian'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(4)
        ->sequence(
            fn ($category) => $category->name->toBe('Dinner'),
            fn ($category) => $category->name->toBe('Healthy'),
            fn ($category) => $category->name->toBe('Main Course'),
            fn ($category) => $category->name->toBe('Italian'),
        );
});

it('reuses existing categories', function () {
    $user = createUser();
    Auth::login($user);

    $existingCategory = Category::factory()->create(['name' => 'Dinner']);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['Dinner, Healthy'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(2)
        ->and($recipe->categories->first()->id)
        ->toBe($existingCategory->id);
});

it('trims whitespace from category names', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['  Dinner  ,  Healthy  '],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(2)
        ->sequence(
            fn ($category) => $category->name->toBe('Dinner'),
            fn ($category) => $category->name->toBe('Healthy'),
        );
});

it('filters out empty category names', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'keywords' => ['Dinner,,  , Healthy'],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->categories)
        ->toHaveCount(2)
        ->sequence(
            fn ($category) => $category->name->toBe('Dinner'),
            fn ($category) => $category->name->toBe('Healthy'),
        );
});
