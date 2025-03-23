<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MeasurementUnit;
use App\ValueObjects\Measurement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeIngredient extends Pivot
{
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * Get the unit attribute.
     */
    public function getUnitAttribute(string|MeasurementUnit|null $value): ?MeasurementUnit
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof MeasurementUnit) {
            return $value;
        }

        return MeasurementUnit::tryFrom($value);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function measurement(): Measurement
    {
        return new Measurement(
            amount: $this->amount,
            unit: $this->unit,
        );
    }
}
