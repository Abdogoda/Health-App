<?php

use App\Enums\ActivityLevelsEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('birth_date');
            $table->float('height', 2)->default(0.0); // in cm
            $table->float('weight', 2)->default(0.0); // in kg
            $table->enum('gender', ['male', 'female']);
            $table->enum('health_goal', ['gain', 'loss', 'stable']);
            $table->enum('activity_level', array_map(fn($e) => $e->value, ActivityLevelsEnum::cases()))->default(ActivityLevelsEnum::SEDENTARY->value);
            $table->string('dietary_preference')->nullable();
            $table->json('dietary_instructions')->nullable();

            $table->integer('daily_caloric_target')->default(0);
            $table->integer('protein_target')->default(0); // in grams
            $table->integer('carbs_target')->default(0); // in grams
            $table->integer('fat_target')->default(0); // in grams

            $table->boolean('receive_daily_report')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
