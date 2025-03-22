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
        Schema::table('recipes', function (Blueprint $table): void {
            $table->string('url')->nullable()->after('description');
            $table->string('author')->nullable()->after('url');
            $table->json('images')->nullable()->after('instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table): void {
            $table->dropColumn(['url', 'author', 'images']);
        });
    }
};
