<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('authenticated users can create a new ingredient', function () {
    $this->actingAs($this->user)
        ->postJson(route('ingredients.store'), [
            'name' => 'New Test Ingredient',
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'name',
        ]);

    $this->assertDatabaseHas('ingredients', [
        'name' => 'New Test Ingredient',
        'is_common' => false,
    ]);
});

test('ingredient name must be unique', function () {
    Ingredient::factory()->create(['name' => 'Existing Ingredient']);

    $this->actingAs($this->user)
        ->postJson(route('ingredients.store'), [
            'name' => 'Existing Ingredient',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('ingredient name is required', function () {
    $this->actingAs($this->user)
        ->postJson(route('ingredients.store'), [
            'name' => '',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated users cannot create ingredients', function () {
    $this->postJson(route('ingredients.store'), [
        'name' => 'New Test Ingredient',
    ])
        ->assertStatus(401);
});
