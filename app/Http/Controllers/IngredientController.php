<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IngredientController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ingredients', 'name')],
        ]);

        $ingredient = Ingredient::query()->create([
            'name' => $validated['name'],
            'is_common' => false,
        ]);

        return response()->json([
            'id' => $ingredient->id,
            'name' => $ingredient->name,
        ]);
    }
}
