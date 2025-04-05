<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meal_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('meal_plan_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_plan_recipe_id')->constrained('meal_plan_recipe')->cascadeOnDelete();
            $table->decimal('servings', 8, 2)->default(1.0)->comment('Number of servings assigned to this day');
            $table->timestamps();

            // A recipe can only be assigned once per day
            $table->unique(['meal_plan_day_id', 'meal_plan_recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_assignments');
    }
};
