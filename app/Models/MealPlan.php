<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class MealPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'duration' => 'integer',
        'people_count' => 'integer',
    ];

    /**
     * Get the user that owns the meal plan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipes for the meal plan.
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->using(MealPlanRecipe::class)
            ->withPivot(['scale_factor'])
            ->withTimestamps();
    }

    /**
     * Get the end date of the meal plan.
     */
    public function getEndDateAttribute(): Carbon
    {
        return $this->start_date->copy()->addDays($this->duration - 1);
    }
}
