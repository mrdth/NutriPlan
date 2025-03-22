<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\CreateRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $recipes = Recipe::query()
            ->with(['user', 'categories'])
            ->latest()
            ->paginate(12);

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Recipes/Create', [
            'categories' => \App\Models\Category::query()->orderBy('name')->get(['id', 'name']),
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

        return redirect()->route('recipes.edit', $recipe)
            ->with('success', 'Recipe created successfully.');
    }

    public function show(Recipe $recipe): Response
    {
        $recipe->load(['user', 'categories', 'ingredients']);

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }

    public function edit(Recipe $recipe): Response
    {
        $this->authorize('update', $recipe);

        $recipe->load(['categories', 'ingredients']);

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
            'categories' => \App\Models\Category::query()->orderBy('name')->get(['id', 'name']),
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

        return redirect()->route('recipes.edit', $recipe)
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
