<?php

declare(strict_types=1);

use App\Models\Recipe;
use App\Models\User;
use App\Policies\RecipePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new RecipePolicy();
});

test('user can view any recipes', function () {
    $user = User::factory()->create();

    expect($this->policy->viewAny($user))->toBeTrue();
});

test('user can view their own private recipe', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->for($user)->create(['is_public' => false]);

    expect($this->policy->view($user, $recipe))->toBeTrue();
});

test('user cannot view other users private recipes', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($otherUser)
        ->create(['is_public' => false]);

    expect($this->policy->view($user, $recipe))->toBeFalse();
});

test('user can view public recipes from other users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($otherUser)
        ->create(['is_public' => true]);

    expect($this->policy->view($user, $recipe))->toBeTrue();
});

test('guest can view public recipes', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($user)
        ->create(['is_public' => true]);

    expect($this->policy->view(null, $recipe))->toBeTrue();
});

test('guest cannot view private recipes', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()
        ->for($user)
        ->create(['is_public' => false]);

    expect($this->policy->view(null, $recipe))->toBeFalse();
});

test('user can create recipe', function () {
    $user = User::factory()->create();

    expect($this->policy->create($user))->toBeTrue();
});

test('user can update their own recipe', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->for($user)->create();

    expect($this->policy->update($user, $recipe))->toBeTrue();
});

test('user cannot update other users recipe', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()->for($otherUser)->create();

    expect($this->policy->update($user, $recipe))->toBeFalse();
});

test('user can delete their own recipe', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->for($user)->create();

    expect($this->policy->delete($user, $recipe))->toBeTrue();
});

test('user cannot delete other users recipe', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()->for($otherUser)->create();

    expect($this->policy->delete($user, $recipe))->toBeFalse();
});
