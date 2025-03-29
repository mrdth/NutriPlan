<?php

declare(strict_types=1);

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('to array only includes visible attributes', function () {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
        ]);
});

test('user has favorites relationship', function () {
    $user = User::factory()->create();

    expect($user->favorites())->toBeInstanceOf(BelongsToMany::class);
    expect($user->favorites)->toBeInstanceOf(Collection::class);
});

test('user can favorite and unfavorite recipes', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

    // Favorite a recipe
    $user->favorites()->attach($recipe);
    expect($user->favorites)->toHaveCount(1);
    expect($user->favorites->first()->id)->toBe($recipe->id);

    // Unfavorite a recipe
    $user->favorites()->detach($recipe);
    $user->refresh();
    expect($user->favorites)->toHaveCount(0);
});
