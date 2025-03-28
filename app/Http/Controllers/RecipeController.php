<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Recipe\CreateRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $user = $request->user();
        $query = Recipe::query()
            ->with(['user', 'categories' => function (Builder|BelongsToMany $query): void {
                $query->withCount('recipes');
            }])
            ->latest();

        // Filter by category if provided in the request
        if ($request->has('category')) {
            $categoryId = $request->input('category');
            $query->whereHas('categories', function (Builder|BelongsToMany $query) use ($categoryId): void {
                $query->where('categories.id', $categoryId);
            });
        }

        $recipes = $query->paginate(12);

        // Add is_favorited flag to each recipe
        $recipes->getCollection()->transform(function (Recipe $recipe) use ($user): Recipe {
            $recipe->is_favorited = $user->favorites()->where('recipe_id', $recipe->id)->exists();
            return $recipe;
        });

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'filter' => $request->only('category'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Recipes/Create', [
            'categories' => \App\Models\Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'ingredients' => \App\Models\Ingredient::query()->orderBy('name')->get(['id', 'name']),
            'measurementUnits' => [
                ['value' => 'g', 'label' => 'Grams'],
                ['value' => 'kg', 'label' => 'Kilograms'],
                ['value' => 'ml', 'label' => 'Milliliters'],
                ['value' => 'l', 'label' => 'Liters'],
                ['value' => 'tsp', 'label' => 'Teaspoons'],
                ['value' => 'tbsp', 'label' => 'Tablespoons'],
                ['value' => 'cup', 'label' => 'Cups'],
                ['value' => 'piece', 'label' => 'Pieces'],
                ['value' => 'pinch', 'label' => 'Pinch'],
            ],
        ]);
    }

    public function store(CreateRecipeRequest $request): RedirectResponse
    {
        $recipe = $request->user()->recipes()->create($request->only([
            'title',
            'description',
            'instructions',
            'prep_time',
            'cooking_time',
            'servings',
            'published_at',
        ]));

        if ($request->has('categories')) {
            $recipe->categories()->sync($request->input('categories'));
        }

        if ($request->has('ingredients')) {
            $recipe->ingredients()->sync(
                collect($request->input('ingredients'))
                    ->mapWithKeys(fn (array $ingredient): array => [
                        $ingredient['ingredient_id'] => [
                            'amount' => $ingredient['amount'],
                            'unit' => $ingredient['unit'],
                        ],
                    ])
                    ->toArray()
            );
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Recipe created successfully.');
    }

    public function show(Recipe $recipe): Response
    {
        $user = request()->user();
        $recipe->load([
            'user',
            'categories' => function (Builder|BelongsToMany $query): void {
                $query->select(['categories.id', 'categories.name', 'categories.slug']);
            },
            'nutritionInformation',
            'ingredients'
        ]);

        // Add is_favorited flag to the recipe
        $recipe->is_favorited = $user->favorites()->where('recipe_id', $recipe->id)->exists();

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }

    public function edit(Recipe $recipe): Response
    {
        $this->authorize('update', $recipe);

        $recipe->load([
            'categories' => function (Builder|BelongsToMany $query): void {
                $query->select(['categories.id', 'categories.name', 'categories.slug']);
            },
            'nutritionInformation',
            'ingredients'
        ]);

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
            'categories' => \App\Models\Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'ingredients' => \App\Models\Ingredient::query()->orderBy('name')->get(['id', 'name']),
            'measurementUnits' => [
                ['value' => 'g', 'label' => 'Grams'],
                ['value' => 'kg', 'label' => 'Kilograms'],
                ['value' => 'ml', 'label' => 'Milliliters'],
                ['value' => 'l', 'label' => 'Liters'],
                ['value' => 'tsp', 'label' => 'Teaspoons'],
                ['value' => 'tbsp', 'label' => 'Tablespoons'],
                ['value' => 'cup', 'label' => 'Cups'],
                ['value' => 'piece', 'label' => 'Pieces'],
                ['value' => 'pinch', 'label' => 'Pinch'],
            ],
        ]);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $this->authorize('update', $recipe);

        $recipe->update($request->safe([
            'title',
            'description',
            'instructions',
            'prep_time',
            'cooking_time',
            'servings',
            'published_at',
        ]));

        if ($request->has('categories')) {
            $recipe->categories()->sync($request->input('categories'));
        }

        if ($request->has('ingredients')) {
            $recipe->ingredients()->sync(
                collect($request->input('ingredients'))
                    ->mapWithKeys(fn (array $ingredient): array => [
                        $ingredient['ingredient_id'] => [
                            'amount' => $ingredient['amount'],
                            'unit' => $ingredient['unit'],
                        ],
                    ])
                    ->toArray()
            );
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $this->authorize('delete', $recipe);

        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }
}
