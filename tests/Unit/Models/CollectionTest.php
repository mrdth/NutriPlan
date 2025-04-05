<?php

declare(strict_types=1);

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;

test('collection belongs to a user', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($collection->user)
        ->toBeInstanceOf(User::class)
        ->id->toBe($user->id);
});

test('collection can have many recipes', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create([
        'user_id' => $user->id,
    ]);
    $recipes = Recipe::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $collection->recipes()->attach($recipes->pluck('id'));

    expect($collection->recipes)
        ->toHaveCount(3)
        ->first()->toBeInstanceOf(Recipe::class);
});

test('collection generates a slug from name', function () {
    $collection = Collection::factory()->create([
        'name' => 'Test Collection Name',
    ]);

    expect($collection->slug)->toBe('test-collection-name');
});

test('collection can be found by slug', function () {
    $collection = Collection::factory()->create([
        'name' => 'Test Collection',
    ]);

    $foundCollection = Collection::query()
        ->where('slug', 'test-collection')
        ->first();

    expect($foundCollection)
        ->not->toBeNull()
        ->id->toBe($collection->id);
});
