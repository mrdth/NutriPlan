<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;

test('recipe generates slug from title', function () {
    $recipe = Recipe::factory()->create([
        'title' => 'Delicious Spaghetti Carbonara',
    ]);

    expect($recipe->slug)->toBe('delicious-spaghetti-carbonara');
});

test('category generates slug from name', function () {
    $category = Category::factory()->create([
        'name' => 'Italian Cuisine',
    ]);

    expect($category->slug)->toBe('italian-cuisine');
});

test('ingredient generates slug from name', function () {
    $ingredient = Ingredient::factory()->create([
        'name' => 'Extra Virgin Olive Oil',
    ]);

    expect($ingredient->slug)->toBe('extra-virgin-olive-oil');
});

test('duplicate titles generate unique slugs', function () {
    $recipe1 = Recipe::factory()->create([
        'title' => 'Classic Pizza',
    ]);

    $recipe2 = Recipe::factory()->create([
        'title' => 'Classic Pizza',
    ]);

    expect($recipe1->slug)->toBe('classic-pizza')
        ->and($recipe2->slug)->not->toBe('classic-pizza')
        ->and($recipe2->slug)->toStartWith('classic-pizza-');
});
