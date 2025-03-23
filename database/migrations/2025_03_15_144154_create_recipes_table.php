<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->integer('servings')->default(0);
            $table->integer('ready_in_minutes')->default(0);
            $table->string('source_url')->nullable();

            $table->boolean('cheap')->default(false);
            $table->boolean('vegetarian')->default(false);
            $table->boolean('vegan')->default(false);
            $table->boolean('very_healthy')->default(false);
            $table->decimal('health_score', 8, 2)->default(0);

            $table->json('cuisines')->nullable();
            $table->json('dish_types')->nullable();
            $table->json('diets')->nullable();
            $table->json('ingredients')->nullable();
            $table->text('instructions')->nullable();

            $table->integer('calories')->default(0);
            $table->integer('protein')->default(0);
            $table->integer('carbs')->default(0);
            $table->integer('fat')->default(0);

            $table->boolean('is_favorite')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
