<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_have_recipes(): void
    {
        $category = Category::factory()->create();
        $recipe = Recipe::factory()->create();

        $category->recipes()->attach($recipe);

        $this->assertTrue($category->recipes->contains($recipe));
    }

    #[Test]
    public function it_can_be_inactive(): void
    {
        $category = Category::factory()->inactive()->create();

        $this->assertFalse($category->is_active);
    }

    #[Test]
    public function it_is_active_by_default(): void
    {
        $category = Category::factory()->create();

        $this->assertTrue($category->is_active);
    }
}
