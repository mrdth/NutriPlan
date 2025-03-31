<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
        });

        // Generate slugs for existing users
        $users = User::all();
        foreach ($users as $user) {
            $user->slug ??= Str::slug($user->name);
            $user->save();
        }

        // Add unique constraint after populating
        Schema::table('users', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('slug');
        });
    }
};
