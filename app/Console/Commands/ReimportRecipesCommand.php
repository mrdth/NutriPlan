<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Actions\FetchRecipe;
use Illuminate\Support\Sleep;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;

class ReimportRecipesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:reimport {--id= : Reimport a specific recipe by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-imports all previously imported recipes or a specific recipe';

    /**
     * Execute the console command.
     */
    public function handle(FetchRecipe $action): int
    {
        $recipeId = $this->option('id');

        if ($recipeId) {
            $recipe = Recipe::find($recipeId);

            if (!$recipe) {
                $this->error("Recipe with ID {$recipeId} not found.");
                return Command::FAILURE;
            }

            $this->reimportRecipe($recipe, $action);
            return Command::SUCCESS;
        }

        $recipes = Recipe::whereNotNull('url')->get();

        if ($recipes->isEmpty()) {
            $this->info('No recipes with URLs found to reimport.');
            return Command::SUCCESS;
        }

        $this->withProgressBar($recipes, function (Recipe $recipe) use ($action): void {
            $this->reimportRecipe($recipe, $action, false);
            Sleep::for(2)->seconds();; // Wait 2 seconds between requests, to avoid being rate-limited
        });

        $this->newLine(2);
        $this->info('Recipe reimport completed.');

        return Command::SUCCESS;
    }

    /**
     * Reimport a single recipe.
     */
    private function reimportRecipe(Recipe $recipe, FetchRecipe $action, bool $showOutput = true): void
    {
        $url = $recipe->url;

        if (empty($url)) {
            if ($showOutput) {
                $this->warn("Recipe '{$recipe->title}' has no URL to reimport from.");
            }
            return;
        }

        try {
            $action->handle($url);

            if ($showOutput) {
                $this->info("Recipe '{$recipe->title}' reimported successfully.");
            }
        } catch (NoStructuredDataException) {
            $message = "No structured data found for recipe '{$recipe->title}' at URL: {$url}";
            Log::warning($message);

            if ($showOutput) {
                $this->warn($message);
            }
        } catch (ConnectionFailedException) {
            $message = "Connection failed for recipe '{$recipe->title}' at URL: {$url}";
            Log::warning($message);

            if ($showOutput) {
                $this->warn($message);
            }
        } catch (\Exception $e) {
            $message = "Error reimporting recipe '{$recipe->title}': {$e->getMessage()}";
            Log::error($message);

            if ($showOutput) {
                $this->error($message);
            }
        }
    }
}
