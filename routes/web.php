<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
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
    Route::post('recipes/import', [RecipeController::class, 'import'])->name('recipes.import');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store');

    Route::resource('collections', CollectionController::class);
    Route::post('collections/add-recipe', [CollectionController::class, 'addRecipe'])->name('collections.add-recipe');
    Route::delete('collections/{collection}/recipes/{recipe}', [CollectionController::class, 'removeRecipe'])->name('collections.remove-recipe');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
