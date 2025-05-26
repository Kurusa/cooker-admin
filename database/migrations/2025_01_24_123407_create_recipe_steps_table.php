<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->text('description');
            $table->text('image_url')->nullable();
            $table->unsignedBigInteger('step_group_id')->nullable();
            $table->timestamps();

            $table->foreign('step_group_id')->references('id')->on('step_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_steps');
    }
};
