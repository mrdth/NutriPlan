<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteRecipeAction
{
    /**
     * Delete a recipe and any related records.
     */
    public function execute(Recipe $recipe): bool
    {
        if (!$recipe->exists) {
            throw new ModelNotFoundException('Recipe not found');
        }

        // Remove related nutrition information
        if ($recipe->nutritionInformation) {
            $recipe->nutritionInformation->delete();
        }

        // The rest of the relationships will be deleted through DB cascades
        // or through the pivot tables automatically
        return $recipe->delete();
    }
}
