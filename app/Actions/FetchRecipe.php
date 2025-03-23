<?php

namespace App\Actions;

use App\Exceptions\RecipeImport\ConnectionFailedException;
use App\Exceptions\RecipeImport\NoStructuredDataException;
use App\Models\Recipe;
use App\Services\RecipeParser;
use Brick\StructuredData\HTMLReader;
use Brick\StructuredData\Reader\JsonLdReader;
use Brick\StructuredData\Reader\MicrodataReader;
use Brick\StructuredData\Reader\RdfaLiteReader;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchRecipe
{
    public function handle(string $recipe_url): Recipe
    {
        try {
            $response = Http::timeout(10)
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36')
                ->get($recipe_url);

            if (!$response->successful()) {
                throw new ConnectionFailedException($recipe_url, "HTTP {$response->status()}");
            }

            // The XML HTML readers don't handle UTF-8 for you
            $html = mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8');

            // We'll try a few different parsers to extract recipe data from the page
            $parsers = [
                new JsonLdReader(),
                new MicrodataReader(),
                new RdfaLiteReader(),
            ];

            $lastError = null;
            foreach ($parsers as $parser) {
                try {
                    $items = (new HTMLReader($parser))->read($html, $recipe_url);
                    if (($recipe = RecipeParser::fromItems($items, $recipe_url)) instanceof \App\Models\Recipe) {
                        return $recipe;
                    }
                } catch (Throwable $e) {
                    $lastError = $e;
                    Log::warning('Parser failed', [
                        'url' => $recipe_url,
                        'parser' => $parser::class,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            // If we get here, no parser found recipe data
            throw new NoStructuredDataException($recipe_url);

        } catch (ConnectionFailedException|NoStructuredDataException $e) {
            // Re-throw these specific exceptions
            throw $e;
        } catch (Throwable $e) {
            Log::error('Recipe import failed', [
                'url' => $recipe_url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new ConnectionFailedException($recipe_url, $e->getMessage());
        }
    }
}
