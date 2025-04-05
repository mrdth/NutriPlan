<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MealPlan;
use App\Models\MealPlanDay;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a meal plan', function () {
    $mealPlan = MealPlan::factory()->create();
    $mealPlanDay = MealPlanDay::factory()->create(['meal_plan_id' => $mealPlan->id]);

    expect($mealPlanDay->mealPlan)->toBeInstanceOf(MealPlan::class)
        ->and($mealPlanDay->mealPlan->id)->toBe($mealPlan->id);
});
