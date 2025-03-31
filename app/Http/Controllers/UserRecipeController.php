<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserRecipeController extends Controller
{
    /**
     * Display a listing of the recipes for a specific user.
     */
    public function index(Request $request, User $user): Response
    {
        $currentUser = $request->user();
        $isOwner = $currentUser->id === $user->id;

        // Build the query to get recipes
        $query = Recipe::query()
            ->with(['user', 'categories' => function (Builder|BelongsToMany $query): void {
                $query->withCount('recipes');
            }])
            ->where('user_id', $user->id)
            ->latest();

        // Filter by category if provided in the request
        if ($request->has('category')) {
            $categoryId = $request->input('category');
            $query->whereHas('categories', function (Builder|BelongsToMany $query) use ($categoryId): void {
                $query->where('categories.id', $categoryId);
            });
        }

        // Handle visibility: if not the recipe owner, only show public recipes
        if (!$isOwner) {
            $query->where('is_public', true);
        }

        $recipes = $query->paginate(12)->withQueryString();

        // Add is_favorited flag to each recipe
        $recipes->getCollection()->transform(function (Recipe $recipe) use ($currentUser): Recipe {
            $recipe->is_favorited = $currentUser->favorites()->where('recipe_id', $recipe->id)->exists();
            return $recipe;
        });

        return Inertia::render('Recipes/UserRecipes', [
            'recipes' => $recipes,
            'filter' => $request->only(['category']),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'slug' => $user->slug,
            ],
            'isOwner' => $isOwner,
        ]);
    }
}
