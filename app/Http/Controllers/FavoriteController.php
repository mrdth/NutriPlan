<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle the favorite status of a recipe for the authenticated user.
     */
    public function __invoke(Request $request, Recipe $recipe): JsonResponse
    {
        $user = $request->user();

        if ($user->favorites()->where('recipe_id', $recipe->id)->exists()) {
            $user->favorites()->detach($recipe);
            $isFavorited = false;
        } else {
            $user->favorites()->attach($recipe);
            $isFavorited = true;
        }

        return response()->json([
            'favorited' => $isFavorited,
        ]);
    }
}
