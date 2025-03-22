<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can have recipes', function () {
    $category = Category::factory()->create();
    $recipe = Recipe::factory()->create();

    $category->recipes()->attach($recipe);

    expect($category->recipes)->toContain($recipe);
});

test('it can be inactive', function () {
    $category = Category::factory()->inactive()->create();

    expect($category->is_active)->toBeFalse();
});

test('it is active by default', function () {
    $category = Category::factory()->create();

    expect($category->is_active)->toBeTrue();
});
