<?php

namespace App\Actions;

use App\Models\Recipe;
use App\Services\RecipeParser;
use Brick\StructuredData\HTMLReader;
use Brick\StructuredData\Reader\JsonLdReader;
use Brick\StructuredData\Reader\MicrodataReader;
use Brick\StructuredData\Reader\RdfaLiteReader;
use Illuminate\Support\Facades\Http;

class FetchRecipe
{
    public function handle(string $recipe_url): ?Recipe
    {
        $response = Http::throw()->get($recipe_url);

        // The XML HTML readers don't handle UTF-8 for you
        $html = mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8');

        // We'll try a few different parser to extract recipe data from the page.
        $parsers = [
            new JsonLdReader(),
            new MicrodataReader(),
            new RdfaLiteReader(),
        ];

        foreach ($parsers as $parser) {
            $items = (new HTMLReader($parser))->read($html, $recipe_url);

            if ($recipe = RecipeParser::fromItems($items, $recipe_url)) {
                break;
            }
        }

        return $recipe;
    }
}
