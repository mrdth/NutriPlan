<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Collection;
use App\Models\Recipe;

class AddRecipeToCollectionAction
{
    public function handle(Collection $collection, Recipe $recipe): void
    {
        if (!$collection->recipes()->where('recipe_id', $recipe->id)->exists()) {
            $collection->recipes()->attach($recipe->id);
        }
    }
}
