<?php

declare(strict_types=1);

use App\Services\NutritionParser;
use Brick\StructuredData\Item;

beforeEach(function () {
    $this->parser = new NutritionParser();
});

it('parses nutrition information from Item objects', function () {
    $item = mock(Item::class);
    $item->shouldReceive('getTypes')->andReturn(['NutritionInformation']);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'calories' => ['240 calories'],
            'carbohydrateContent' => ['37 grams carbohydrates'],
            'proteinContent' => ['4g protein'],
            'fatContent' => ['9g fat'],
            'fiberContent' => ['2g fiber'],
            'sugarContent' => ['5g sugar'],
            'cholesterolContent' => ['0mg cholesterol'],
            'sodiumContent' => ['200mg of sodium'],
            'saturatedFatContent' => ['2g saturated fat'],
            'transFatContent' => ['0g trans fat'],
            'unsaturatedFatContent' => ['7g unsaturated fat'],
            'servingSize' => ['1 serving'],
        ]);

    $nutrition = $this->parser->parse([$item]);

    expect($nutrition)
        ->toBeArray()
        ->and($nutrition['calories'])->toBe('240 cal')
        ->and($nutrition['carbohydrate_content'])->toBe('37 g')
        ->and($nutrition['protein_content'])->toBe('4 g')
        ->and($nutrition['fat_content'])->toBe('9 g')
        ->and($nutrition['fiber_content'])->toBe('2 g')
        ->and($nutrition['sugar_content'])->toBe('5 g')
        ->and($nutrition['cholesterol_content'])->toBe('0 mg')
        ->and($nutrition['sodium_content'])->toBe('200 mg')
        ->and($nutrition['saturated_fat_content'])->toBe('2 g')
        ->and($nutrition['trans_fat_content'])->toBe('0 g')
        ->and($nutrition['unsaturated_fat_content'])->toBe('7 g')
        ->and($nutrition['serving_size'])->toBe('1 serving');
});

it('parses nutrition information from array data', function () {
    $data = [
        '@type' => 'NutritionInformation',
        'calories' => '240 calories',
        'carbohydrateContent' => '37 grams carbohydrates',
        'proteinContent' => '4g protein',
        'fatContent' => '9g fat',
        'fiberContent' => '2g fiber',
        'sugarContent' => '5g sugar',
        'cholesterolContent' => '0mg cholesterol',
        'sodiumContent' => '200mg of sodium',
        'saturatedFatContent' => '2g saturated fat',
        'transFatContent' => '0g trans fat',
        'unsaturatedFatContent' => '7g unsaturated fat',
        'servingSize' => '1 serving',
    ];

    $nutrition = $this->parser->parse([$data]);

    expect($nutrition)
        ->toBeArray()
        ->and($nutrition['calories'])->toBe('240 cal')
        ->and($nutrition['carbohydrate_content'])->toBe('37 g')
        ->and($nutrition['protein_content'])->toBe('4 g')
        ->and($nutrition['fat_content'])->toBe('9 g')
        ->and($nutrition['fiber_content'])->toBe('2 g')
        ->and($nutrition['sugar_content'])->toBe('5 g')
        ->and($nutrition['cholesterol_content'])->toBe('0 mg')
        ->and($nutrition['sodium_content'])->toBe('200 mg')
        ->and($nutrition['saturated_fat_content'])->toBe('2 g')
        ->and($nutrition['trans_fat_content'])->toBe('0 g')
        ->and($nutrition['unsaturated_fat_content'])->toBe('7 g')
        ->and($nutrition['serving_size'])->toBe('1 serving');
});

it('parses nested nutrition information', function () {
    $data = [
        '@type' => 'Recipe',
        'name' => 'Test Recipe',
        'nutrition' => [
            '@type' => 'NutritionInformation',
            'calories' => '240 calories',
            'proteinContent' => '10g protein',
        ],
    ];

    $nutrition = $this->parser->parse([$data]);

    expect($nutrition)
        ->toBeArray()
        ->and($nutrition['calories'])->toBe('240 cal')
        ->and($nutrition['protein_content'])->toBe('10 g');
});

it('handles missing nutrition information', function () {
    $item = mock(Item::class);
    $item->shouldReceive('getTypes')->andReturn(['Recipe']);
    $item->shouldReceive('getProperties')
        ->andReturn([
            'name' => ['Test Recipe'],
            'recipeIngredient' => ['1 cup flour', '2 eggs'],
        ]);

    $nutrition = $this->parser->parse([$item]);

    expect($nutrition)->toBeNull();
});

it('handles numeric values by adding appropriate units', function () {
    $data = [
        '@type' => 'NutritionInformation',
        'calories' => '240',
        'proteinContent' => '10',
        'sodiumContent' => '200',
    ];

    $nutrition = $this->parser->parse([$data]);

    expect($nutrition)
        ->toBeArray()
        ->and($nutrition['calories'])->toBe('240 cal')
        ->and($nutrition['protein_content'])->toBe('10 g')
        ->and($nutrition['sodium_content'])->toBe('200 mg');
});
