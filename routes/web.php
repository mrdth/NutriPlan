<?php

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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
