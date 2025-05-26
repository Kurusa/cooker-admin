<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('complexity')->nullable();
            $table->text('advice')->nullable();
            $table->integer('time')->nullable();
            $table->integer('portions')->nullable();
            $table->text('image_url')->nullable();
            $table->foreignId('source_recipe_url_id')->constrained('source_recipe_urls')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
