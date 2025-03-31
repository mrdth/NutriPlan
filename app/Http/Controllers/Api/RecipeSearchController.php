<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeSearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        if (empty($query)) {
            return response()->json([
                'data' => [],
            ]);
        }

        $recipes = Recipe::query()
            ->where(function (Builder $q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where(function (Builder $q) use ($request): void {
                // Show only public recipes or user's own recipes
                /** @var \App\Models\User $user */
                $user = $request->user();
                $q->where('is_public', true)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['user:id,name,slug'])
            ->select(['id', 'title', 'slug', 'servings', 'images', 'user_id'])
            ->orderBy('title')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $recipes,
        ]);
    }
}
