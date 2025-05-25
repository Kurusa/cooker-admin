<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ingredient_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->foreignId('ingredient_group_id')
                ->nullable()
                ->after('recipe_id')
                ->constrained('ingredient_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropForeign(['ingredient_group_id']);
            $table->dropColumn('ingredient_group_id');
        });

        Schema::dropIfExists('ingredient_groups');
    }
};
