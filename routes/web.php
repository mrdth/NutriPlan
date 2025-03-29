<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionRecipeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeImportController;
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

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store');

    Route::resource('collections', CollectionController::class);
    Route::post('collections/add-recipe', [CollectionRecipeController::class, 'store'])->name('collections.add-recipe');
    Route::delete('collections/{collection}/recipes/{recipe}', [CollectionRecipeController::class, 'destroy'])->name('collections.remove-recipe');

    Route::post('recipes/{recipe}/favorite', FavoriteController::class)->name('recipes.favorite');
    Route::get('favorites', [FavoritesController::class, 'index'])->name('favorites.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
