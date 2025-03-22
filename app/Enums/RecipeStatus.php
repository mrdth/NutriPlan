<?php

declare(strict_types=1);

namespace App\Enums;

enum RecipeStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green',
            self::ARCHIVED => 'red',
        };
    }

    public static function fromPublishedAt(?string $publishedAt): self
    {
        if ($publishedAt === null) {
            return self::DRAFT;
        }

        return self::PUBLISHED;
    }
}
