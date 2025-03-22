<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $casts = [
        'is_common' => 'boolean',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->withPivot(['amount', 'unit']);
    }
}
