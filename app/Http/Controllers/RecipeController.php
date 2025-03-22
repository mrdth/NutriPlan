<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\CreateRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
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
        return Inertia::render('Recipes/Create');
    }

    public function store(CreateRecipeRequest $request): RedirectResponse
    {
        $recipe = $request->user()->recipes()->create($request->validated());

        if ($request->has('categories')) {
            $recipe->categories()->sync($request->input('categories'));
        }

        if ($request->has('ingredients')) {
            $recipe->ingredients()->sync($request->input('ingredients'));
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
        $recipe->load(['categories', 'ingredients']);

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
        ]);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        $recipe->update($request->validated());

        if ($request->has('categories')) {
            $recipe->categories()->sync($request->input('categories'));
        }

        if ($request->has('ingredients')) {
            $recipe->ingredients()->sync($request->input('ingredients'));
        }

        return redirect()->route('recipes.edit', $recipe)
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }
}
