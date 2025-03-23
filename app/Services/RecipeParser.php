<?php

namespace App\Services;

use App\Models\Recipe;
use Brick\StructuredData\Item;
use Exception;
use Illuminate\Support\Str;

final class RecipeParser
{
    public static function fromItems($items, $url): ?Recipe
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
        protected $totalTime = '',
        protected $images = []
    ) {
    }

    public function parse(Item $item): Recipe
    {
        foreach ($item->getProperties() as $name => $values) {
            $fn = "parse_" . Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
            if (method_exists($this, $fn)) {
                $this->$fn($values);
            }
        }

        return Recipe::firstOrNew([
            'title' => $this->title,
            'url' => $this->url,
        ])->fill([
            'author' => $this->author,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'steps' => $this->steps,
            'yield' => $this->yield,
            'totalTime' => $this->totalTime,
            'images' => $this->images,
        ]);
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
        $this->yield = (is_array($values) ? $values[0] : $values);
    }

    public function parse_totaltime($values): void
    {
        $this->totalTime = (is_array($values) ? $values[0] : $values);
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
