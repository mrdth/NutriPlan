<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionRecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a recipe can be added to a collection.
     */
    public function test_store_adds_a_recipe_to_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('collections.add-recipe'), [
                'collection_id' => $collection->id,
                'recipe_id' => $recipe->id,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('collection_recipe', [
            'collection_id' => $collection->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    /**
     * Test that a recipe can be removed from a collection.
     */
    public function test_destroy_removes_a_recipe_from_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $collection->recipes()->attach($recipe->id);

        $this->actingAs($user)
            ->delete(route('collections.remove-recipe', [
                'collection' => $collection,
                'recipe' => $recipe,
            ]))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('collection_recipe', [
            'collection_id' => $collection->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    /**
     * Test that a user cannot add a recipe to another user's collection.
     */
    public function test_user_cannot_add_recipe_to_others_collections(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('collections.add-recipe'), [
                'collection_id' => $collection->id,
                'recipe_id' => $recipe->id,
            ])
            ->assertForbidden();
    }

    /**
     * Test that a user cannot remove a recipe from another user's collection.
     */
    public function test_user_cannot_remove_recipe_from_others_collections(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $collection->recipes()->attach($recipe->id);

        $this->actingAs($user)
            ->delete(route('collections.remove-recipe', [
                'collection' => $collection,
                'recipe' => $recipe,
            ]))
            ->assertForbidden();
    }
}
