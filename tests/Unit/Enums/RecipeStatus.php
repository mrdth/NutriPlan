<?php

declare(strict_types=1);

use App\Enums\RecipeStatus;

test('it provides human readable labels', function () {
    expect(RecipeStatus::DRAFT->label())->toBe('Draft');
    expect(RecipeStatus::PUBLISHED->label())->toBe('Published');
    expect(RecipeStatus::ARCHIVED->label())->toBe('Archived');
});

test('it provides color codes', function () {
    expect(RecipeStatus::DRAFT->color())->toBe('gray');
    expect(RecipeStatus::PUBLISHED->color())->toBe('green');
    expect(RecipeStatus::ARCHIVED->color())->toBe('red');
});

test('it can be created from published at date', function () {
    expect(RecipeStatus::fromPublishedAt(null))->toBe(RecipeStatus::DRAFT);
    expect(RecipeStatus::fromPublishedAt('2025-03-22 12:00:00'))->toBe(RecipeStatus::PUBLISHED);
});
