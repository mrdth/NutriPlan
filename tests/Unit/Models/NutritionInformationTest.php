<?php

declare(strict_types=1);

use App\Models\NutritionInformation;
use App\Models\Recipe;

test('nutrition information belongs to a recipe', function () {
    $recipe = Recipe::factory()->create();
    $nutritionInfo = NutritionInformation::factory()->create([
        'recipe_id' => $recipe->id,
    ]);

    expect($nutritionInfo->recipe)->toBeInstanceOf(Recipe::class)
        ->and($nutritionInfo->recipe->id)->toBe($recipe->id);
});

test('nutrition information can be created with all fields', function () {
    $nutritionInfo = NutritionInformation::factory()->create([
        'calories' => '240 calories',
        'carbohydrate_content' => '37g',
        'protein_content' => '4g',
        'fat_content' => '9g',
        'fiber_content' => '2g',
        'sugar_content' => '5g',
        'cholesterol_content' => '0mg',
        'sodium_content' => '200mg',
        'saturated_fat_content' => '2g',
        'trans_fat_content' => '0g',
        'unsaturated_fat_content' => '7g',
        'serving_size' => '1 serving',
    ]);

    expect($nutritionInfo->calories)->toBe('240 calories')
        ->and($nutritionInfo->carbohydrate_content)->toBe('37g')
        ->and($nutritionInfo->protein_content)->toBe('4g')
        ->and($nutritionInfo->fat_content)->toBe('9g')
        ->and($nutritionInfo->fiber_content)->toBe('2g')
        ->and($nutritionInfo->sugar_content)->toBe('5g')
        ->and($nutritionInfo->cholesterol_content)->toBe('0mg')
        ->and($nutritionInfo->sodium_content)->toBe('200mg')
        ->and($nutritionInfo->saturated_fat_content)->toBe('2g')
        ->and($nutritionInfo->trans_fat_content)->toBe('0g')
        ->and($nutritionInfo->unsaturated_fat_content)->toBe('7g')
        ->and($nutritionInfo->serving_size)->toBe('1 serving');
});

test('nutrition information can be updated', function () {
    $nutritionInfo = NutritionInformation::factory()->create([
        'calories' => '240 calories',
    ]);

    $nutritionInfo->update([
        'calories' => '300 calories',
    ]);

    $nutritionInfo->refresh();

    expect($nutritionInfo->calories)->toBe('300 calories');
});

test('nutrition information can be deleted', function () {
    $nutritionInfo = NutritionInformation::factory()->create();
    $id = $nutritionInfo->id;

    $nutritionInfo->delete();

    expect(NutritionInformation::find($id))->toBeNull();
});
