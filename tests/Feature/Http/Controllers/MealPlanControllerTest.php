<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\TestCase; // Assuming TestCase uses RefreshDatabase

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates meal plan days when storing a new meal plan', function () {
    $startDate = Carbon::today();
    $duration = 7;
    $mealPlanData = [
        'name' => 'Test Weekly Plan',
        'start_date' => $startDate->toDateString(),
        'duration' => $duration,
        'people_count' => 2,
    ];

    $response = $this->post(route('meal-plans.store'), $mealPlanData);

    $response->assertRedirect(route('meal-plans.index'));
    $response->assertSessionHas('success');

    $mealPlan = MealPlan::query()->where('user_id', $this->user->id)->latest()->first();

    expect($mealPlan)->not->toBeNull()
        ->and($mealPlan->name)->toBe($mealPlanData['name'])
        ->and($mealPlan->start_date->toDateString())->toBe($startDate->toDateString())
        ->and($mealPlan->duration)->toBe($duration)
        ->and($mealPlan->people_count)->toBe($mealPlanData['people_count'])
        ->and($mealPlan->days)->toHaveCount($duration);

    // Check if days are created correctly
    foreach (range(1, $duration) as $dayNumber) {
        expect($mealPlan->days()->where('day_number', $dayNumber)->exists())->toBeTrue();
    }
});

it('loads meal plan days when showing a meal plan', function () {
    $mealPlan = MealPlan::factory()->for($this->user)->create();
    // Manually create days for testing, as the factory won't trigger the controller logic
    for ($i = 1; $i <= $mealPlan->duration; $i++) {
        $mealPlan->days()->create(['day_number' => $i]);
    }

    $response = $this->get(route('meal-plans.show', $mealPlan));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
        ->component('MealPlans/Show')
        ->has(
            'mealPlan',
            fn ($prop) => $prop
            ->where('id', $mealPlan->id)
            ->has('days', $mealPlan->duration)
            ->etc()
        )
    );
});
