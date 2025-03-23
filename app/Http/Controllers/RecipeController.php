<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Recipe;
use App\Actions\FetchRecipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Recipe\CreateRecipeRequest;
use App\Http\Requests\Recipe\ImportRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;

class RecipeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
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
        $recipe->load([
            'user',
            'categories' => function (Builder|BelongsToMany $query): void {
                $query->select(['categories.id', 'categories.name', 'categories.slug']);
            },
            'ingredients'
        ]);

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

    public function import(ImportRecipeRequest $request, FetchRecipe $action): RedirectResponse
    {
        try {
            $recipe = $action->handle($request->input('url'));

            return redirect()->route('recipes.show', $recipe)
                ->with('success', 'Recipe imported successfully. Please review and make any necessary adjustments.');

        } catch (NoStructuredDataException) {
            return back()->withErrors([
                'url' => 'We could not find any recipe data on this page. The website may not use standard recipe markup.',
            ]);

        } catch (ConnectionFailedException) {
            return back()->withErrors([
                'url' => 'Could not connect to the recipe website. Please check the URL and try again.',
            ]);

        } catch (\Exception $e) {
            report($e); // Log unexpected errors

            return back()->withErrors([
                'url' => 'An unexpected error occurred while importing the recipe. Please try again later.',
            ]);
        }
    }
}
