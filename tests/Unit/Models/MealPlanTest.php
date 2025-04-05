<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MealPlan;
use App\Models\MealPlanDay;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has many days', function () {
    $mealPlan = MealPlan::factory()->create();
    $days = MealPlanDay::factory()->count(3)->create(['meal_plan_id' => $mealPlan->id]);

    expect($mealPlan->days)->toBeInstanceOf(Collection::class)
        ->and($mealPlan->days)->toHaveCount(3)
        ->and($mealPlan->days->first())->toBeInstanceOf(MealPlanDay::class);
});

it('orders days by day_number', function () {
    $mealPlan = MealPlan::factory()->create();
    $day3 = MealPlanDay::factory()->create(['meal_plan_id' => $mealPlan->id, 'day_number' => 3]);
    $day1 = MealPlanDay::factory()->create(['meal_plan_id' => $mealPlan->id, 'day_number' => 1]);
    $day2 = MealPlanDay::factory()->create(['meal_plan_id' => $mealPlan->id, 'day_number' => 2]);

    $days = $mealPlan->days;

    expect($days)->toHaveCount(3)
        ->and($days[0]->id)->toBe($day1->id)
        ->and($days[1]->id)->toBe($day2->id)
        ->and($days[2]->id)->toBe($day3->id);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $mealPlan = MealPlan::factory()->create(['user_id' => $user->id]);

    expect($mealPlan->user)->toBeInstanceOf(User::class)
        ->and($mealPlan->user->id)->toBe($user->id);
});
