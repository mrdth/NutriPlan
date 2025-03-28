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
    private ?array $nutrition = null;
    private ?Recipe $recipe = null;

    public function __construct(
        private string $title = '',
        private string $description = '',
        private readonly string $url = '',
        private string $author = '',
        /** @var array<int, string> */
        private array $ingredients = [],
        /** @var array<int, string> */
        private array $steps = [],
        private string $yield = '',
        private int $prep_time = 0,
        private int $cooking_time = 0,
        private int $servings = 0,
        /** @var array<int, string> */
        private array $images = [],
        /** @var array<int, string> */
        private array $categories = [],
        private readonly IngredientParser $ingredient_parser = new IngredientParser()
    ) {
    }

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


    /**
     * Set an existing recipe to update
     */
    public function setRecipe(Recipe $recipe): self
    {
        $this->recipe = $recipe;
        return $this;
    }

    public function parse(Item $item): Recipe
    {
        foreach ($item->getProperties() as $name => $values) {
            $fn = "parse_" . Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));

            if (method_exists($this, $fn)) {
                $this->$fn($values);
            }

            // Special handling for nutrition data which might be under different property names
            if (Str::contains(Str::lower($name), 'nutrition') && ($this->nutrition === null || $this->nutrition === [])) {
                $this->parse_nutrition([$values]);
            }
        }

        // Use the existing recipe if set, otherwise find or create a new one
        $recipe = $this->recipe ?? Recipe::firstOrNew([
            'title' => $this->title,
            'url' => $this->url,
        ])->fill([
            'author' => $this->author,
            'description' => $this->description,
            'instructions' => implode("\n\n", $this->steps),
            'prep_time' => $this->prep_time,
            'cooking_time' => $this->cooking_time,
            'servings' => $this->servings !== 0 ? $this->servings : (int) $this->yield,
            'images' => array_values(array_filter($this->images)),
            'user_id' => Auth::id(),
        ]);

        // Save the recipe first if it's new
        if (!$recipe->exists) {
            $recipe->save();
        }

        // Handle categories
        $category_names = array_unique(array_filter($this->categories));
        $categories = collect($category_names)->map(fn (string $name): Category => Category::firstOrCreate(
            ['name' => trim($name)],
            ['is_active' => true]
        ));

        $recipe->categories()->sync($categories->pluck('id'));

        // Parse and attach ingredients
        $ingredients_data = [];
        foreach ($this->ingredients as $ingredient_string) {
            $parsed = $this->ingredient_parser->parse($ingredient_string);
            $ingredients_data[$parsed['ingredient']->id] = [
                'amount' => $parsed['amount'],
                'unit' => $parsed['unit'],
            ];
        }

        $recipe->ingredients()->sync($ingredients_data);

        // Handle nutrition information if available
        if ($this->nutrition !== null && $this->nutrition !== []) {
            if ($recipe->nutritionInformation) {
                // Update existing nutrition information
                $recipe->nutritionInformation->update($this->nutrition);
            } else {
                // Create new nutrition information
                $recipe->nutritionInformation()->create($this->nutrition);
            }

            // Refresh the recipe to ensure the nutrition information is loaded
            $recipe->refresh();
        }

        return $recipe;
    }

    /**
     * @param array<int, string>|string $values
     */
    private function parse_name(array|string $values): void
    {
        $this->title = (is_array($values) ? $values[0] : $values);
    }

    /**
     * @param array<int, string>|string $values
     */
    private function parse_description(array|string $values): void
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
                    // If it's relative
                    if ($name == "url" && Str::contains($values[0], ["http://", "https://"])) {
                        $this->images[] = $values[0];
                    }
                }
            } elseif (is_array($item)) {
                throw new Exception("Handle image items are array of strings");
            } elseif (Str::contains($item, ["http://", "https://"])) {
                $this->images[] = $item;
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
                collect($values)->transform(fn (string $item): string => html_entity_decode($item))->toArray()
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
    private function parseCommaSeparatedString(string $value): array
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

    /**
     * Cleans up nutrition values by removing redundant information
     */
    private function cleanNutritionValue(string $type, string $value): string
    {
        // Define patterns to clean up for each nutrition type
        $patterns = [
            'calories' => ['/\s*calories\s*$/i'],
            'carbohydrate_content' => ['/\s*(grams?|g)\s*(carbohydrates?|carbs?)\s*$/i', '/\s*carbohydrates?\s*$/i'],
            'protein_content' => ['/\s*(grams?|g)\s*protein\s*$/i', '/\s*protein\s*$/i'],
            'fat_content' => ['/\s*(grams?|g)\s*fat\s*$/i', '/\s*fat\s*$/i'],
            'fiber_content' => ['/\s*(grams?|g)\s*fiber\s*$/i', '/\s*fiber\s*$/i'],
            'sugar_content' => ['/\s*(grams?|g)\s*sugar\s*$/i', '/\s*sugar\s*$/i'],
            'sodium_content' => ['/\s*(milligrams?|mg)\s*of\s*sodium\s*$/i', '/\s*sodium\s*$/i'],
            'saturated_fat_content' => ['/\s*(grams?|g)\s*saturated\s*fat\s*$/i', '/\s*saturated\s*fat\s*$/i'],
            'trans_fat_content' => ['/\s*(grams?|g)\s*trans\s*fat\s*$/i', '/\s*trans\s*fat\s*$/i'],
            'unsaturated_fat_content' => ['/\s*(grams?|g)\s*unsaturated\s*fat\s*$/i', '/\s*unsaturated\s*fat\s*$/i'],
            'cholesterol_content' => ['/\s*(milligrams?|mg)\s*cholesterol\s*$/i', '/\s*cholesterol\s*$/i'],
        ];

        // Apply patterns if they exist for this nutrition type
        if (isset($patterns[$type])) {
            foreach ($patterns[$type] as $pattern) {
                $value = preg_replace($pattern, '', (string) $value);
            }

            // Trim any remaining whitespace
            $value = trim((string) $value);

            // Add units if they're missing
            if (is_numeric($value)) {
                $value = match ($type) {
                    'calories' => $value . ' cal',
                    'sodium_content', 'cholesterol_content' => $value . ' mg',
                    default => $value . ' g',
                };
            }
        }

        return $value;
    }

    /**
     * @param array<int, Item|string|array> $values
     */
    public function parse_nutrition(array $values): void
    {
        // If we already have nutrition data, don't overwrite it
        if ($this->nutrition !== null && $this->nutrition !== []) {
            return;
        }

        $nutritionData = [];

        foreach ($values as $item) {
            // Handle Item objects (from structured data parsers)
            if ($item instanceof Item) {
                // Check if this is a nutrition information item
                $isNutritionItem = false;
                $types = $item->getTypes();
                foreach ($types as $type) {
                    if (Str::contains(Str::lower($type), 'nutrition')) {
                        $isNutritionItem = true;
                        break;
                    }
                }

                foreach ($item->getProperties() as $name => $propValues) {
                    $propertyName = Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));

                    // Map schema.org property names to our database column names
                    $mappedName = match ($propertyName) {
                        'calories' => 'calories',
                        'carbohydratecontent' => 'carbohydrate_content',
                        'cholesterolcontent' => 'cholesterol_content',
                        'fatcontent' => 'fat_content',
                        'fibercontent' => 'fiber_content',
                        'proteincontent' => 'protein_content',
                        'saturatedfatcontent' => 'saturated_fat_content',
                        'servingsize' => 'serving_size',
                        'sodiumcontent' => 'sodium_content',
                        'sugarcontent' => 'sugar_content',
                        'transfatcontent' => 'trans_fat_content',
                        'unsaturatedfatcontent' => 'unsaturated_fat_content',
                        default => null,
                    };

                    if ($mappedName && !empty($propValues)) {
                        $value = is_array($propValues) ? $propValues[0] : $propValues;
                        $nutritionData[$mappedName] = $this->cleanNutritionValue($mappedName, html_entity_decode((string) $value));
                    }
                }

                // If this is a nutrition item and we found data, use it
                if ($isNutritionItem && $nutritionData !== []) {
                    $this->nutrition = $nutritionData;
                    return;
                }
            }
            // Handle array data (from JSON-LD)
            elseif (is_array($item)) {
                // Check if this is a nutrition information item
                $isNutritionItem = false;
                if (isset($item['@type'])) {
                    $isNutritionItem = Str::contains(Str::lower($item['@type']), 'nutrition');
                }

                // Handle direct array of nutrition data
                foreach ($item as $key => $value) {
                    if ($key === '@type') {
                        continue;
                    }
                    if ($key === '@context') {
                        continue;
                    }
                    $propertyName = Str::lower($key);

                    // Map schema.org property names to our database column names
                    $mappedName = match ($propertyName) {
                        'calories' => 'calories',
                        'carbohydratecontent' => 'carbohydrate_content',
                        'cholesterolcontent' => 'cholesterol_content',
                        'fatcontent' => 'fat_content',
                        'fibercontent' => 'fiber_content',
                        'proteincontent' => 'protein_content',
                        'saturatedfatcontent' => 'saturated_fat_content',
                        'servingsize' => 'serving_size',
                        'sodiumcontent' => 'sodium_content',
                        'sugarcontent' => 'sugar_content',
                        'transfatcontent' => 'trans_fat_content',
                        'unsaturatedfatcontent' => 'unsaturated_fat_content',
                        default => null,
                    };

                    if ($mappedName && !empty($value)) {
                        $cleanValue = is_array($value) ? html_entity_decode((string) $value[0]) : html_entity_decode((string) $value);
                        $nutritionData[$mappedName] = $this->cleanNutritionValue($mappedName, $cleanValue);
                    }
                }

                // Check for nested nutrition object
                if (isset($item['nutrition']) && is_array($item['nutrition'])) {
                    foreach ($item['nutrition'] as $key => $value) {
                        if ($key === '@type') {
                            continue;
                        }
                        if ($key === '@context') {
                            continue;
                        }
                        $propertyName = Str::lower($key);

                        // Map schema.org property names to our database column names
                        $mappedName = match ($propertyName) {
                            'calories' => 'calories',
                            'carbohydratecontent' => 'carbohydrate_content',
                            'cholesterolcontent' => 'cholesterol_content',
                            'fatcontent' => 'fat_content',
                            'fibercontent' => 'fiber_content',
                            'proteincontent' => 'protein_content',
                            'saturatedfatcontent' => 'saturated_fat_content',
                            'servingsize' => 'serving_size',
                            'sodiumcontent' => 'sodium_content',
                            'sugarcontent' => 'sugar_content',
                            'transfatcontent' => 'trans_fat_content',
                            'unsaturatedfatcontent' => 'unsaturated_fat_content',
                            default => null,
                        };

                        if ($mappedName && !empty($value)) {
                            $cleanValue = is_array($value) ? html_entity_decode((string) $value[0]) : html_entity_decode((string) $value);
                            $nutritionData[$mappedName] = $this->cleanNutritionValue($mappedName, $cleanValue);
                        }
                    }
                }

                // If this is a nutrition item or we found data, use it
                if (($isNutritionItem || isset($item['nutrition'])) && $nutritionData !== []) {
                    $this->nutrition = $nutritionData;
                    return;
                }
            }
        }

        // If we found any nutrition data, save it
        if ($nutritionData !== []) {
            $this->nutrition = $nutritionData;
        }
    }
}
