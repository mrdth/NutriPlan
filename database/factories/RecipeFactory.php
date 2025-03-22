<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Recipe>
     */
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'url' => fake()->optional()->url(),
            'author' => fake()->optional()->name(),
            'instructions' => fake()->paragraphs(3, true),
            'images' => fake()->optional()->randomElements([
                fake()->imageUrl(),
                fake()->imageUrl(),
                fake()->imageUrl(),
                fake()->imageUrl(),
            ], fake()->numberBetween(1, 4)),
            'prep_time' => fake()->numberBetween(5, 60),
            'cooking_time' => fake()->numberBetween(10, 180),
            'servings' => fake()->numberBetween(1, 8),
            'published_at' => fake()->optional()->dateTimeBetween('-1 year'),
        ];
    }

    /**
     * Indicate that the recipe is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the recipe is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
