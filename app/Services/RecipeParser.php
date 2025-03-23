<?php

namespace App\Services;

use Exception;
use App\Models\Recipe;
use Illuminate\Support\Str;
use Brick\StructuredData\Item;
use Illuminate\Support\Facades\Auth;

final class RecipeParser
{
    public static function fromItems($items, string $url): ?Recipe
    {
        foreach ($items as $item) {
            if (Str::contains(Str::lower(implode(',', $item->getTypes())), 'recipe')) {
                return (new self(url: $url))->parse($item);
            }
        }

        // The whole thing might be a recipe
        if (count($items) == 1) {
            return (new self(url: $url))->parse(is_array($items) ? $items[0] : $items);
        }

        return null;
    }

    public function __construct(
        protected $title = '',
        protected $description = '',
        protected $url = '',
        protected $author = '',
        protected $ingredients = [],
        protected $steps = [],
        protected $yield = '',
        protected $prep_time = 0,
        protected $cooking_time = 0,
        protected $servings = 0,
        protected $images = [],
        protected ?IngredientParser $ingredient_parser = null
    ) {
        $this->ingredient_parser = $ingredient_parser ?? new IngredientParser();
    }

    public function parse(Item $item): Recipe
    {
        foreach ($item->getProperties() as $name => $values) {
            $fn = "parse_" . Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
            if (method_exists($this, $fn)) {
                $this->$fn($values);
            }
        }

        $recipe = Recipe::firstOrNew([
            'title' => $this->title,
            'url' => $this->url,
        ])->fill([
            'author' => $this->author,
            'description' => $this->description,
            'instructions' => implode("\n\n", $this->steps),
            'prep_time' => $this->prep_time,
            'cooking_time' => $this->cooking_time,
            'servings' => $this->servings ?: (int) $this->yield,
            'images' => array_values(array_filter($this->images)),
            'user_id' => Auth::id(),
        ]);

        // Parse and attach ingredients
        $ingredients_data = [];
        foreach ($this->ingredients as $ingredient_string) {
            $parsed = $this->ingredient_parser->parse($ingredient_string);
            $ingredients_data[$parsed['ingredient']->id] = [
                'amount' => $parsed['amount'],
                'unit' => $parsed['unit'],
            ];
        }

        if ($recipe->exists) {
            $recipe->ingredients()->sync($ingredients_data);
        } else {
            $recipe->save();
            $recipe->ingredients()->attach($ingredients_data);
        }

        return $recipe;
    }

    protected function parse_name($values): void
    {
        $this->title = (is_array($values) ? $values[0] : $values);
    }

    protected function parse_description($values): void
    {
        $this->description = (is_array($values) ? $values[0] : $values);
    }

    public function parse_recipeyield($values): void
    {
        $value = is_array($values) ? $values[0] : $values;

        // Try to extract a number from the yield string
        if (preg_match('/\d+/', $value, $matches)) {
            $this->servings = (int) $matches[0];
        }

        $this->yield = $value;
    }

    public function parse_preptime($values): void
    {
        $time = is_array($values) ? $values[0] : $values;

        if (preg_match('/PT(\d+H)?(\d+M)?/', $time, $matches)) {
            $hours = isset($matches[1]) ? (int) rtrim($matches[1], 'H') : 0;
            $minutes = isset($matches[2]) ? (int) rtrim($matches[2], 'M') : 0;

            $this->prep_time = ($hours * 60) + $minutes;
        }
    }

    public function parse_cooktime($values): void
    {
        $time = is_array($values) ? $values[0] : $values;

        if (preg_match('/PT(\d+H)?(\d+M)?/', $time, $matches)) {
            $hours = isset($matches[1]) ? (int) rtrim($matches[1], 'H') : 0;
            $minutes = isset($matches[2]) ? (int) rtrim($matches[2], 'M') : 0;

            $this->cooking_time = ($hours * 60) + $minutes;
        }
    }

    public function parse_image($values): void
    {
        foreach ($values as $item) {
            if ($item instanceof Item) {
                foreach ($item->getProperties() as $name => $values) {
                    $name = Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
                    // $name may be one of [url, height, thumbnail, width]
                    if ($name == "url") {
                        // If it's relative
                        if (Str::contains($values[0], ["http://", "https://"])) {
                            $this->images[] = $values[0];
                        }
                    }
                }
            } else {
                if (is_array($item)) {
                    throw new Exception("Handle image items are array of strings");
                } else {
                    if (Str::contains($item, ["http://", "https://"])) {
                        $this->images[] = $item;
                    }
                }
            }
        }
    }

    public function parse_recipeingredient($values): void
    {
        if (is_array($values)) {
            $this->ingredients = array_merge(
                collect($values)->transform(function ($item) {
                    return html_entity_decode($item);
                })->toArray()
            );
        }
    }

    public function parse_recipeinstructions($values): void
    {
        foreach ($values as $item) {
            if ($item instanceof Item) {
                if (Str::contains(Str::lower(implode(',', $item->getTypes())), 'howtostep')) {
                    foreach ($item->getProperties() as $name => $values) {
                        $name = Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
                        if ($name == "text") {
                            $this->steps[] = html_entity_decode($values[0]);
                        }
                    }
                }
            } else {
                $this->steps[] = html_entity_decode($item);
            }
        }
    }

    public function parse_author($values): void
    {
        foreach ($values as $item) {
            if ($item instanceof Item) {
                if (Str::contains(Str::lower(implode(',', $item->getTypes())), 'person')) {
                    foreach ($item->getProperties() as $name => $values) {
                        $name = Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
                        if ($name == "name") {
                            $this->author = html_entity_decode($values[0]);
                        }
                    }
                }
            } else {
                $this->author = html_entity_decode($item);
            }
        }
    }
}
