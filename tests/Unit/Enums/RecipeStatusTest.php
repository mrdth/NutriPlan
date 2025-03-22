<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\RecipeStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecipeStatusTest extends TestCase
{
    #[Test]
    public function it_provides_human_readable_labels(): void
    {
        $this->assertEquals('Draft', RecipeStatus::DRAFT->label());
        $this->assertEquals('Published', RecipeStatus::PUBLISHED->label());
        $this->assertEquals('Archived', RecipeStatus::ARCHIVED->label());
    }

    #[Test]
    public function it_provides_color_codes(): void
    {
        $this->assertEquals('gray', RecipeStatus::DRAFT->color());
        $this->assertEquals('green', RecipeStatus::PUBLISHED->color());
        $this->assertEquals('red', RecipeStatus::ARCHIVED->color());
    }

    #[Test]
    public function it_can_be_created_from_published_at_date(): void
    {
        $this->assertEquals(RecipeStatus::DRAFT, RecipeStatus::fromPublishedAt(null));
        $this->assertEquals(RecipeStatus::PUBLISHED, RecipeStatus::fromPublishedAt('2025-03-22 12:00:00'));
    }
}
