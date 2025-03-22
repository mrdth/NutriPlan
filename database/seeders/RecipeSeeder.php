<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MeasurementUnit;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Create some common categories
        $categories = [
            'Breakfast',
            'Lunch',
            'Dinner',
            'Dessert',
            'Snacks',
            'Vegetarian',
            'Vegan',
            'Gluten Free',
            'Quick & Easy',
            'Healthy',
        ];

        $categoryModels = collect($categories)->map(fn ($name) => Category::factory()->create([
            'name' => $name,
            'description' => fake()->sentence(),
        ]));

        // Create some common ingredients
        $ingredients = [
            'Salt',
            'Black Pepper',
            'Olive Oil',
            'Garlic',
            'Onion',
            'Butter',
            'Flour',
            'Sugar',
            'Eggs',
            'Milk',
            'Rice',
            'Pasta',
            'Chicken',
            'Beef',
            'Tomatoes',
            'Potatoes',
            'Carrots',
            'Celery',
            'Lemon',
            'Parsley',
        ];

        $ingredientModels = collect($ingredients)->map(fn ($name) => Ingredient::factory()->common()->create([
            'name' => $name,
            'description' => fake()->sentence(),
        ]));

        // Create some additional random ingredients
        $additionalIngredients = Ingredient::factory()->count(30)->create();
        $allIngredients = $ingredientModels->merge($additionalIngredients);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create recipes with random categories and ingredients
        Recipe::factory()
            ->count(50)
            ->create(['user_id' => $user->id])
            ->each(function (Recipe $recipe) use ($categoryModels, $allIngredients): void {
                // Attach 1-3 random categories
                $recipe->categories()->attach(
                    $categoryModels->random(fake()->numberBetween(1, 3))->pluck('id')
                );

                // Attach 3-10 random ingredients with amounts
                $recipe->ingredients()->attach(
                    $allIngredients->random(fake()->numberBetween(3, 10))->mapWithKeys(fn($ingredient) => [
                        $ingredient->id => [
                            'amount' => fake()->randomFloat(2, 0.25, 10),
                            'unit' => fake()->randomElement(array_column(MeasurementUnit::cases(), 'value')),
                        ],
                    ])->toArray()
                );
            });
    }
}
