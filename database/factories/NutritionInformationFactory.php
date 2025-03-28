<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NutritionInformation>
 */
class NutritionInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'calories' => $this->faker->numberBetween(100, 800) . ' cal',
            'carbohydrate_content' => $this->faker->numberBetween(5, 100) . ' g',
            'cholesterol_content' => $this->faker->numberBetween(0, 200) . ' mg',
            'fat_content' => $this->faker->numberBetween(1, 40) . ' g',
            'fiber_content' => $this->faker->numberBetween(0, 15) . ' g',
            'protein_content' => $this->faker->numberBetween(1, 50) . ' g',
            'saturated_fat_content' => $this->faker->numberBetween(0, 20) . ' g',
            'serving_size' => $this->faker->randomElement(['1 serving', '100g', '1 cup', '1 slice']),
            'sodium_content' => $this->faker->numberBetween(10, 1000) . ' mg',
            'sugar_content' => $this->faker->numberBetween(0, 30) . ' g',
            'trans_fat_content' => $this->faker->numberBetween(0, 5) . ' g',
            'unsaturated_fat_content' => $this->faker->numberBetween(0, 20) . ' g',
        ];
    }
}
