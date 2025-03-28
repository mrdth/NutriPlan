<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\FetchRecipe;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Recipe\ImportRecipeRequest;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;

class RecipeImportController extends Controller
{
    /**
     * Import a recipe from a URL.
     *
     * This controller handles the process of scraping recipe data
     * from external websites using structured data formats (JSON-LD, Microdata, RDFa Lite).
     * It uses the FetchRecipe action to extract and process the recipe data.
     *
     * @param ImportRecipeRequest $request The validated request containing the recipe URL
     * @param FetchRecipe $action The action to handle the recipe fetching and parsing
     */
    public function __invoke(ImportRecipeRequest $request, FetchRecipe $action): RedirectResponse
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
