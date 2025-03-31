<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();

        $categories = Category::query()
            ->withCount(['recipes' => function (Builder $query) use ($user): void {
                // Only count recipes that are public or owned by the current user
                $query->where(function (Builder $query) use ($user): void {
                    $query->where('is_public', true);

                    if ($user) {
                        $query->orWhere('user_id', $user->id);
                    }
                });
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->filter(fn (Category $category): bool => $category->recipes_count > 0)
            ->values();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category): Response
    {
        $user = request()->user();

        $recipes = Recipe::query()
            ->whereHas('categories', function (Builder $query) use ($category): void {
                $query->where('categories.id', $category->id);
            })
            ->where(function (Builder $query) use ($user): void {
                $query->where('is_public', true);

                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->with(['user:id,name,slug', 'categories'])
            ->latest()
            ->paginate(12);

        // Add is_favorited flag if user is logged in
        if ($user) {
            $recipes->getCollection()->transform(function (Recipe $recipe) use ($user): Recipe {
                $recipe->is_favorited = $user->favorites()->where('recipe_id', $recipe->id)->exists();
                return $recipe;
            });
        }

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'category' => $category,
        ]);
    }
}
