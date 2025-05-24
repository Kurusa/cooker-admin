<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuisines', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->timestamps();
        });

        Schema::create('cuisine_recipe', function (Blueprint $table) {
            $table->foreignId('cuisine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();

            $table->primary(['cuisine_id', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuisine_recipe');
        Schema::dropIfExists('cuisines');
    }
};
