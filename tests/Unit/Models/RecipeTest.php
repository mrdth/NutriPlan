<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\MeasurementUnit;
use App\Enums\RecipeStatus;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use App\ValueObjects\Measurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $recipe = Recipe::factory()->create();

        $this->assertInstanceOf(User::class, $recipe->user);
    }

    #[Test]
    public function it_can_be_published(): void
    {
        $recipe = Recipe::factory()->published()->create();

        $this->assertNotNull($recipe->published_at);
    }

    #[Test]
    public function it_can_be_a_draft(): void
    {
        $recipe = Recipe::factory()->draft()->create();

        $this->assertNull($recipe->published_at);
        $this->assertEquals(RecipeStatus::DRAFT, $recipe->status);
    }

    #[Test]
    public function it_can_get_measurement_for_ingredient(): void
    {
        $recipe = Recipe::factory()->create();
        $ingredient = Ingredient::factory()->create();

        $recipe->ingredients()->attach($ingredient, [
            'amount' => 2.5,
            'unit' => MeasurementUnit::CUP->value,
        ]);

        $measurement = $recipe->getMeasurementForIngredient($ingredient);

        $this->assertInstanceOf(Measurement::class, $measurement);
        $this->assertEquals(2.5, $measurement->amount);
        $this->assertEquals(MeasurementUnit::CUP, $measurement->unit);
    }

    #[Test]
    public function it_returns_null_measurement_for_non_existent_ingredient(): void
    {
        $recipe = Recipe::factory()->create();
        $ingredient = Ingredient::factory()->create();

        $this->assertNull($recipe->getMeasurementForIngredient($ingredient));
    }
}
