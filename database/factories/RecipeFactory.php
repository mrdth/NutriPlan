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
        $title = fake()->unique()->sentence(3);

        return [
            'user_id' => User::factory(),

            'title' => $title,
            'description' => fake()->paragraph(),
            'url' => fake()->optional()->url(),
            'author' => fake()->optional()->name(),
            'instructions' => fake()->paragraphs(3, true),
            'images' => fake()->optional()->randomElements([
                'https://placehold.co/600x400?text=' . urlencode($title),
                'https://placehold.co/600x400?text=' . urlencode($title),
                'https://placehold.co/600x400?text=' . urlencode($title),
                'https://placehold.co/600x400?text=' . urlencode($title),
            ], fake()->numberBetween(1, 4)),
            'prep_time' => fake()->numberBetween(5, 60),
            'cooking_time' => fake()->numberBetween(10, 180),
            'servings' => fake()->numberBetween(1, 8),
        ];
    }
}
