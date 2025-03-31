<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()
            ->withCount('recipes')
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
        $recipes = Recipe::query()
            ->whereHas('categories', function (Builder $query) use ($category): void {
                $query->where('categories.id', $category->id);
            })
            ->with(['user:id,name,slug', 'categories'])
            ->latest()
            ->paginate(12);

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'category' => $category,
        ]);
    }
}
