<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CollectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_user_collections(): void
    {
        $user = User::factory()->create();
        $collections = Collection::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $otherUserCollection = Collection::factory()->create();

        $this->actingAs($user)
            ->get(route('collections.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                ->component('Collections/Index')
                ->has('collections', 3)
                ->where('collections.0.id', $collections[0]->id)
                ->where('collections.0.name', $collections[0]->name)
            );
    }

    public function test_index_returns_json_when_requested(): void
    {
        $user = User::factory()->create();
        $collections = Collection::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('collections.index'))
            ->assertOk()
            ->assertJsonStructure(['collections']);

        $this->assertCount(3, $response->json('collections'));
    }

    public function test_show_displays_collection_with_recipes(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $recipes = Recipe::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $collection->recipes()->attach($recipes->pluck('id'));

        $this->actingAs($user)
            ->get(route('collections.show', $collection))
            ->assertInertia(
                fn (Assert $page) => $page
                ->component('Collections/Show')
                ->has('collection')
                ->where('collection.id', $collection->id)
                ->where('collection.name', $collection->name)
                ->has('collection.recipes', 2)
            );
    }

    public function test_store_creates_a_new_collection(): void
    {
        $user = User::factory()->create();
        $collectionData = [
            'name' => 'My Test Collection',
            'description' => 'A collection for testing',
        ];

        $this->actingAs($user)
            ->post(route('collections.store'), $collectionData)
            ->assertRedirect(route('collections.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('collections', [
            'user_id' => $user->id,
            'name' => $collectionData['name'],
            'description' => $collectionData['description'],
        ]);
    }

    public function test_update_updates_a_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Collection Name',
            'description' => 'Updated description',
        ];

        $this->actingAs($user)
            ->put(route('collections.update', $collection), $updatedData)
            ->assertRedirect(route('collections.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => $updatedData['name'],
            'description' => $updatedData['description'],
        ]);
    }

    public function test_destroy_deletes_a_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->delete(route('collections.destroy', $collection))
            ->assertRedirect(route('collections.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('collections', [
            'id' => $collection->id,
        ]);
    }



    public function test_user_cannot_view_others_collections(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('collections.show', $collection))
            ->assertForbidden();
    }

    public function test_user_cannot_update_others_collections(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->put(route('collections.update', $collection), [
                'name' => 'Updated Name',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_delete_others_collections(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->delete(route('collections.destroy', $collection))
            ->assertForbidden();
    }
}
