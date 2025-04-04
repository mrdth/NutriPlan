<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionRecipeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeImportController;
use App\Http\Controllers\UserRecipeController;
use App\Http\Controllers\MealPlanRecipeController;
use App\Http\Controllers\MealAssignmentController;
use App\Http\Controllers\MealPlanCopyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('recipes', RecipeController::class);
    Route::post('recipes/import', RecipeImportController::class)->name('recipes.import');
    Route::get('recipes/by/{user}', [UserRecipeController::class, 'index'])->name('recipes.by-user');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store');

    Route::resource('collections', CollectionController::class);
    Route::post('collections/add-recipe', [CollectionRecipeController::class, 'store'])->name('collections.add-recipe');
    Route::delete('collections/{collection}/recipes/{recipe}', [CollectionRecipeController::class, 'destroy'])->name('collections.remove-recipe');

    Route::post('recipes/{recipe}/favorite', FavoriteController::class)->name('recipes.favorite');
    Route::get('favorites', [FavoritesController::class, 'index'])->name('favorites.index');

    Route::resource('meal-plans', MealPlanController::class)->except(['edit', 'update'])->parameters([
        'meal-plans' => 'mealPlan'
    ]);
    Route::post('meal-plans/add-recipe', [MealPlanRecipeController::class, 'store'])->name('meal-plans.add-recipe');
    Route::post('meal-plans/{mealPlan}/copy', MealPlanCopyController::class)->name('meal-plans.copy');

    // Fix the parameter names to match the controller expectations
    Route::delete('meal-plans/{id}/recipes/{recipeId}', [MealPlanRecipeController::class, 'destroy'])
         ->name('meal-plans.remove-recipe');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/meal-assignments', [MealAssignmentController::class, 'store'])->name('meal-assignments.store');
    Route::put('/meal-assignments/{mealAssignment}', [MealAssignmentController::class, 'update'])->name('meal-assignments.update');
    Route::delete('/meal-assignments/{mealAssignment}', [MealAssignmentController::class, 'destroy'])->name('meal-assignments.destroy');
    Route::post('/meal-assignments/{mealAssignment}/toggle-cook', [MealAssignmentController::class, 'toggleToCook'])->name('meal-assignments.toggle-cook');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
