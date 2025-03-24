<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\AddRecipeToCollectionAction;
use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddRecipeToCollectionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_recipe_to_a_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $action = new AddRecipeToCollectionAction();
        $action->handle($collection, $recipe);

        $this->assertDatabaseHas('collection_recipe', [
            'collection_id' => $collection->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_it_does_not_add_duplicate_recipe_to_collection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        // Add the recipe to the collection
        $collection->recipes()->attach($recipe->id);

        // Try to add it again
        $action = new AddRecipeToCollectionAction();
        $action->handle($collection, $recipe);

        // Check that the recipe is in the collection only once
        $this->assertCount(1, $collection->recipes);
        $this->assertEquals(1, $collection->recipes()->where('recipe_id', $recipe->id)->count());
    }
}
