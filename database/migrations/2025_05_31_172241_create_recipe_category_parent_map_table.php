<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipe_category_parent_map', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('parent_id');

            $table->foreign('category_id')->references('id')->on('recipe_categories')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('recipe_categories')->onDelete('cascade');

            $table->unique(['category_id', 'parent_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_category_parent_map');
    }
};
