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
        Schema::table('meal_assignments', function (Blueprint $table): void {
            $table->boolean('to_cook')->default(false)->after('servings')
                ->comment('Flag indicating if this meal assignment needs to be cooked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_assignments', function (Blueprint $table): void {
            $table->dropColumn('to_cook');
        });
    }
};
