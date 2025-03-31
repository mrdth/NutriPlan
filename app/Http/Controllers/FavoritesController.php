<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the user's favorite recipes.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $favorites = $user->favorites()
            ->with(['user:id,name,slug', 'categories' => function (Builder|BelongsToMany $query): void {
                $query->withCount('recipes')
                    ->orderBy('recipes_count', 'desc');
            }])
            ->withCount('ingredients')
            ->paginate(12)
            ->withQueryString();

        // Add is_favorited flag to each recipe
        $favorites->getCollection()->transform(function (Recipe $recipe): Recipe {
            $recipe->is_favorited = true;
            return $recipe;
        });

        return Inertia::render('Recipes/Favorites', [
            'favorites' => $favorites,
        ]);
    }
}
