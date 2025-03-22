<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MeasurementUnit;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Ingredient>
     */
    protected $model = Ingredient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
        
        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'is_common' => false,
        ];
    }

    /**
     * Indicate that the ingredient is commonly found in kitchens.
     */
    public function common(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_common' => true,
        ]);
    }

    /**
     * Create an ingredient with its relationship to a recipe.
     */
    public function forRecipe(): static
    {
        return $this->state(fn (array $attributes) => [])
            ->afterCreating(function (Ingredient $ingredient) {
                $ingredient->recipes()->attach(
                    Recipe::factory()->create(),
                    [
                        'amount' => fake()->randomFloat(2, 0.25, 10),
                        'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                    ]
                );
            });
    }
}
