<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_recipe_url_excluded_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            $table->string('rule_type');
            $table->text('value');
            $table->timestamps();

            $table->index('source_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_recipe_url_excluded_rules');
    }
};
