<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\Measurement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'cooking_time' => 'integer',
        'prep_time' => 'integer',
        'servings' => 'integer',
        'images' => 'array',
    ];

    protected $hidden = [
        'user_id',
        'updated_at',
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

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    public function nutritionInformation(): HasOne
    {
        return $this->hasOne(NutritionInformation::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recipe_user_favorites')
            ->withTimestamps();
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
