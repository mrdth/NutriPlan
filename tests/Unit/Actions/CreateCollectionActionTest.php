<?php

declare(strict_types=1);

use App\Actions\CreateCollectionAction;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a collection for a user', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'Test Collection',
        'description' => 'This is a test collection',
    ];

    $action = new CreateCollectionAction();
    $collection = $action->handle($user, $data);

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->name->toBe($data['name'])
        ->description->toBe($data['description'])
        ->user_id->toBe($user->id)
        ->slug->not->toBeNull();

    $this->assertDatabaseHas('collections', [
        'user_id' => $user->id,
        'name' => $data['name'],
        'description' => $data['description'],
    ]);
});

test('it creates a collection without description', function () {
    $user = User::factory()->create();
    $data = [
        'name' => 'Test Collection',
    ];

    $action = new CreateCollectionAction();
    $collection = $action->handle($user, $data);

    expect($collection)
        ->toBeInstanceOf(Collection::class)
        ->name->toBe($data['name'])
        ->description->toBeNull()
        ->user_id->toBe($user->id);

    $this->assertDatabaseHas('collections', [
        'user_id' => $user->id,
        'name' => $data['name'],
        'description' => null,
    ]);
});
