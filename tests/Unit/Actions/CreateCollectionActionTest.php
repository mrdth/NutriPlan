<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateCollectionAction;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCollectionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_collection_for_a_user(): void
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Test Collection',
            'description' => 'This is a test collection',
        ];

        $action = new CreateCollectionAction();
        $collection = $action->handle($user, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($data['name'], $collection->name);
        $this->assertEquals($data['description'], $collection->description);
        $this->assertEquals($user->id, $collection->user_id);
        $this->assertNotNull($collection->slug);

        $this->assertDatabaseHas('collections', [
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    public function test_it_creates_a_collection_without_description(): void
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Test Collection',
        ];

        $action = new CreateCollectionAction();
        $collection = $action->handle($user, $data);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($data['name'], $collection->name);
        $this->assertNull($collection->description);
        $this->assertEquals($user->id, $collection->user_id);

        $this->assertDatabaseHas('collections', [
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => null,
        ]);
    }
}
