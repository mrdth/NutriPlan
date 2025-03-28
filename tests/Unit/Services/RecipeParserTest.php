<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\NutritionInformation;
use App\Models\Recipe;
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

it('parses nutrition information', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getTypes')->andReturn(['Recipe']);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'name' => ['Test Recipe'],
            'nutrition' => [
                [
                    'calories' => '240 calories',
                    'carbohydrateContent' => '37g',
                    'proteinContent' => '4g',
                    'fatContent' => '9g',
                    'fiberContent' => '2g',
                    'sugarContent' => '5g',
                    'cholesterolContent' => '0mg',
                    'sodiumContent' => '200mg',
                    'saturatedFatContent' => '2g',
                    'transFatContent' => '0g',
                    'unsaturatedFatContent' => '7g',
                    'servingSize' => '1 serving',
                ]
            ],
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->nutritionInformation)
        ->toBeInstanceOf(NutritionInformation::class)
        ->and($recipe->nutritionInformation->calories)->toBe('240 cal')
        ->and($recipe->nutritionInformation->carbohydrate_content)->toBe('37g')
        ->and($recipe->nutritionInformation->protein_content)->toBe('4g')
        ->and($recipe->nutritionInformation->fat_content)->toBe('9g')
        ->and($recipe->nutritionInformation->fiber_content)->toBe('2g')
        ->and($recipe->nutritionInformation->sugar_content)->toBe('5g')
        ->and($recipe->nutritionInformation->cholesterol_content)->toBe('0mg')
        ->and($recipe->nutritionInformation->sodium_content)->toBe('200mg')
        ->and($recipe->nutritionInformation->saturated_fat_content)->toBe('2g')
        ->and($recipe->nutritionInformation->trans_fat_content)->toBe('0g')
        ->and($recipe->nutritionInformation->unsaturated_fat_content)->toBe('7g')
        ->and($recipe->nutritionInformation->serving_size)->toBe('1 serving');
});

it('updates existing nutrition information', function () {
    $user = createUser();
    Auth::login($user);

    $recipe = Recipe::factory()->create();
    $existingNutrition = NutritionInformation::factory()->create([
        'recipe_id' => $recipe->id,
        'calories' => '200 cal',
        'protein_content' => '5g',
    ]);

    $item = mock(Item::class);
    $item->shouldReceive('getTypes')->andReturn(['Recipe']);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'name' => [$recipe->title],
            'url' => [$recipe->url],
            'nutrition' => [
                [
                    'calories' => '240 calories',
                    'proteinContent' => '10g',
                ],
            ],
        ]);

    $this->parser->setRecipe($recipe);
    $updatedRecipe = $this->parser->parse($item);

    expect($updatedRecipe->id)->toBe($recipe->id)
        ->and($updatedRecipe->nutritionInformation->id)->toBe($existingNutrition->id)
        ->and($updatedRecipe->nutritionInformation->calories)->toBe('240 cal')
        ->and($updatedRecipe->nutritionInformation->protein_content)->toBe('10g');
});

it('handles missing nutrition information', function () {
    $user = createUser();
    Auth::login($user);

    $item = mock(Item::class);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'name' => 'Test Recipe',
        ]);

    $recipe = $this->parser->parse($item);

    expect($recipe->nutritionInformation)->toBeNull();
});
