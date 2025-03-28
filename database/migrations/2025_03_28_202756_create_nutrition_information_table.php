<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nutrition_information', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('calories')->nullable();
            $table->string('carbohydrate_content')->nullable();
            $table->string('cholesterol_content')->nullable();
            $table->string('fat_content')->nullable();
            $table->string('fiber_content')->nullable();
            $table->string('protein_content')->nullable();
            $table->string('saturated_fat_content')->nullable();
            $table->string('serving_size')->nullable();
            $table->string('sodium_content')->nullable();
            $table->string('sugar_content')->nullable();
            $table->string('trans_fat_content')->nullable();
            $table->string('unsaturated_fat_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_information');
    }
};
