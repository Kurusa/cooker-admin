<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('ingredient_unit_id')->constrained('ingredient_units')->cascadeOnDelete();
            $table->float('quantity')->nullable();

            $table->primary(['recipe_id', 'ingredient_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
