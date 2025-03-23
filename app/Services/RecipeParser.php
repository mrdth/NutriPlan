<?php

namespace App\Services;

use Exception;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Support\Str;
use Brick\StructuredData\Item;
use Illuminate\Support\Facades\Auth;

final class RecipeParser
{
    /**
     * @param array<int, Item>|Item $items
     */
    public static function fromItems(array|Item $items, string $url): ?Recipe
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
        protected string $title = '',
        protected string $description = '',
        protected string $url = '',
        protected string $author = '',
        /** @var array<int, string> */
        protected array $ingredients = [],
        /** @var array<int, string> */
        protected array $steps = [],
        protected string $yield = '',
        protected int $prep_time = 0,
        protected int $cooking_time = 0,
        protected int $servings = 0,
        /** @var array<int, string> */
        protected array $images = [],
        /** @var array<int, string> */
        protected array $categories = [],
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

        // Handle categories
        $category_names = array_unique(array_filter($this->categories));
        $categories = collect($category_names)->map(function (string $name): Category {
            return Category::firstOrCreate(
                ['name' => trim($name)],
                ['is_active' => true]
            );
        });

        if ($recipe->exists) {
            $recipe->categories()->sync($categories->pluck('id'));
        } else {
            $recipe->save();
            $recipe->categories()->attach($categories->pluck('id'));
        }

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

    /**
     * @param array<int, string>|string $values
     */
    protected function parse_name(array|string $values): void
    {
        $this->title = (is_array($values) ? $values[0] : $values);
    }

    /**
     * @param array<int, string>|string $values
     */
    protected function parse_description(array|string $values): void
    {
        $this->description = (is_array($values) ? $values[0] : $values);
    }

    /**
     * @param array<int, string>|string $values
     */
    public function parse_recipeyield(array|string $values): void
    {
        $value = is_array($values) ? $values[0] : $values;

        // Try to extract a number from the yield string
        if (preg_match('/\d+/', $value, $matches)) {
            $this->servings = (int) $matches[0];
        }

        $this->yield = $value;
    }

    /**
     * @param array<int, string>|string $values
     */
    public function parse_preptime(array|string $values): void
    {
        $time = is_array($values) ? $values[0] : $values;

        if (preg_match('/PT(\d+H)?(\d+M)?/', $time, $matches)) {
            $hours = isset($matches[1]) ? (int) rtrim($matches[1], 'H') : 0;
            $minutes = isset($matches[2]) ? (int) rtrim($matches[2], 'M') : 0;

            $this->prep_time = ($hours * 60) + $minutes;
        }
    }

    /**
     * @param array<int, string>|string $values
     */
    public function parse_cooktime(array|string $values): void
    {
        $time = is_array($values) ? $values[0] : $values;

        if (preg_match('/PT(\d+H)?(\d+M)?/', $time, $matches)) {
            $hours = isset($matches[1]) ? (int) rtrim($matches[1], 'H') : 0;
            $minutes = isset($matches[2]) ? (int) rtrim($matches[2], 'M') : 0;

            $this->cooking_time = ($hours * 60) + $minutes;
        }
    }

    /**
     * @param array<int, Item|string> $values
     */
    public function parse_image(array $values): void
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

    /**
     * @param array<int, string>|string $values
     */
    public function parse_recipeingredient(array|string $values): void
    {
        if (is_array($values)) {
            $this->ingredients = array_merge(
                collect($values)->transform(function (string $item): string {
                    return html_entity_decode($item);
                })->toArray()
            );
        }
    }

    /**
     * @param array<int, Item|string> $values
     */
    public function parse_recipeinstructions(array $values): void
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

    /**
     * @param array<int, Item|string> $values
     */
    public function parse_author(array $values): void
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

    /**
     * @return array<int, string>
     */
    protected function parseCommaSeparatedString(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->toArray();
    }

    /**
     * @param array<int, string|array<int, string>>|string $values
     */
    public function parse_keywords(array|string $values): void
    {
        if (is_array($values)) {
            $keywords = collect($values)->map(function (string|array $item): array|string {
                $value = is_array($item) ? $item[0] : $item;
                return Str::contains($value, ',')
                    ? $this->parseCommaSeparatedString($value)
                    : $value;
            })->flatten()->toArray();

            $this->categories = array_merge($this->categories, $keywords);
        } else {
            $categories = Str::contains($values, ',')
                ? $this->parseCommaSeparatedString($values)
                : [$values];

            $this->categories = array_merge($this->categories, $categories);
        }
    }

    /**
     * @param array<int, string|array<int, string>>|string $values
     */
    public function parse_recipecategory(array|string $values): void
    {
        if (is_array($values)) {
            $categories = collect($values)->map(function (string|array $item): array|string {
                $value = is_array($item) ? $item[0] : $item;
                return Str::contains($value, ',')
                    ? $this->parseCommaSeparatedString($value)
                    : $value;
            })->flatten()->toArray();

            $this->categories = array_merge($this->categories, $categories);
        } else {
            $categories = Str::contains($values, ',')
                ? $this->parseCommaSeparatedString($values)
                : [$values];

            $this->categories = array_merge($this->categories, $categories);
        }
    }
}
