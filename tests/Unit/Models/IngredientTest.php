<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_added_to_a_recipe_with_amount_and_unit(): void
    {
        $ingredient = Ingredient::factory()->create();
        $recipe = Recipe::factory()->create();

        $ingredient->recipes()->attach($recipe, [
            'amount' => 2.5,
            'unit' => 'cups',
        ]);

        $this->assertTrue($ingredient->recipes->contains($recipe));
        $this->assertEquals(2.5, $ingredient->recipes->first()->pivot->amount);
        $this->assertEquals('cups', $ingredient->recipes->first()->pivot->unit);
    }

    #[Test]
    public function it_can_be_marked_as_common(): void
    {
        $ingredient = Ingredient::factory()->common()->create();

        $this->assertTrue($ingredient->is_common);
    }

    #[Test]
    public function it_is_not_common_by_default(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->assertFalse($ingredient->is_common);
    }
}
