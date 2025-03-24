<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Recipe;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Actions\CreateCollectionAction;
use App\Actions\AddRecipeToCollectionAction;
use App\Http\Requests\CreateCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CollectionController extends Controller
{
    public function index(Request $request): Response|\Illuminate\Http\JsonResponse
    {
        $collections = Collection::query()
            ->where('user_id', auth()->id())
            ->withCount('recipes')
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'collections' => $collections,
            ]);
        }

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
        ]);
    }

    public function show(Collection $collection): Response
    {
        Gate::authorize('view', $collection);

        $collection->load(['recipes.categories' => function (BelongsToMany $query) {
            $query->latest();
        }]);

        return Inertia::render('Collections/Show', [
            'collection' => $collection,
        ]);
    }

    public function store(CreateCollectionRequest $request, CreateCollectionAction $action): RedirectResponse
    {
        $user = $request->user();
        $collection = $action->handle($user, $request->validated());

        return redirect()->route('collections.index')
            ->with('success', 'Collection created successfully.');
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $collection->update($request->validated());

        return redirect()->route('collections.index')
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        Gate::authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    public function addRecipe(Request $request, AddRecipeToCollectionAction $action): RedirectResponse
    {
        $request->validate([
            'collection_id' => ['required', 'exists:collections,id'],
            'recipe_id' => ['required', 'exists:recipes,id'],
        ]);

        $collection = Collection::query()->findOrFail($request->input('collection_id'));
        Gate::authorize('update', $collection);

        $recipe = Recipe::query()->findOrFail($request->input('recipe_id'));

        $action->handle($collection, $recipe);

        return back()->with('success', 'Recipe added to collection successfully.');
    }

    public function removeRecipe(Collection $collection, Recipe $recipe): RedirectResponse
    {
        Gate::authorize('update', $collection);

        $collection->recipes()->detach($recipe->id);

        return back()->with('success', 'Recipe removed from collection successfully.');
    }
}
