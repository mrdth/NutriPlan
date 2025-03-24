<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $collection->user);
        $this->assertEquals($user->id, $collection->user->id);
    }

    public function test_it_can_have_many_recipes(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);
        $recipes = Recipe::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $collection->recipes()->attach($recipes->pluck('id'));

        $this->assertCount(3, $collection->recipes);
        $this->assertInstanceOf(Recipe::class, $collection->recipes->first());
    }

    public function test_it_generates_a_slug_from_name(): void
    {
        $collection = Collection::factory()->create([
            'name' => 'Test Collection Name',
        ]);

        $this->assertEquals('test-collection-name', $collection->slug);
    }

    public function test_it_can_find_by_slug(): void
    {
        $collection = Collection::factory()->create([
            'name' => 'Test Collection',
        ]);

        $foundCollection = Collection::query()->where('slug', 'test-collection')->first();

        $this->assertNotNull($foundCollection);
        $this->assertEquals($collection->id, $foundCollection->id);
    }
}
