<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DeleteRecipeAction;
use App\Models\Recipe;
use App\Models\NutritionInformation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteRecipeActionTest extends TestCase
{
    use RefreshDatabase;

    public function testItDeletesRecipeAndRelatedRecords(): void
    {
        // Arrange
        $recipe = Recipe::factory()->create();
        $nutritionInfo = NutritionInformation::factory()->create([
            'recipe_id' => $recipe->id,
        ]);

        // Act
        $action = new DeleteRecipeAction();
        $result = $action->execute($recipe);

        // Assert
        $this->assertTrue($result);
        $this->assertModelMissing($recipe);
        $this->assertModelMissing($nutritionInfo);
    }

    public function testItThrowsExceptionWhenRecipeDoesNotExist(): void
    {
        // Arrange
        $recipe = Recipe::factory()->make(); // Not persisted to database
        $recipe->id = 999; // Invalid ID

        // Assert
        $this->expectException(ModelNotFoundException::class);

        // Act
        $action = new DeleteRecipeAction();
        $action->execute($recipe);
    }
}
