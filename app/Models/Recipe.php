<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecipeStatus;
use App\ValueObjects\Measurement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Recipe extends Model
{
    use HasFactory;
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected $casts = [
        'cooking_time' => 'integer',
        'prep_time' => 'integer',
        'servings' => 'integer',
        'published_at' => 'datetime',
        'images' => 'array',
    ];

    protected $appends = [
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot(['amount', 'unit'])
            ->using(RecipeIngredient::class);
    }

    public function getStatusAttribute(): RecipeStatus
    {
        return RecipeStatus::fromPublishedAt($this->published_at?->toDateTimeString());
    }

    public function getMeasurementForIngredient(Ingredient $ingredient): ?Measurement
    {
        $pivot = $this->ingredients->find($ingredient)?->pivot;

        if (! $pivot) {
            return null;
        }

        return Measurement::from(
            amount: (float) $pivot->amount,
            unit: $pivot->unit->value,
        );
    }
}
