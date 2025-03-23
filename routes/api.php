<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ProfileController;
use App\Http\Controllers\API\HealthProfileController;
use App\Http\Controllers\API\MealPlanController;
use App\Http\Controllers\API\ProgressController;
use App\Http\Controllers\API\PublicController;
use App\Http\Controllers\API\RecipeController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

    Route::prefix('/profile')->group(function () {
        // Info Profile routes
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);

        // Health profile routes
        Route::get('/health', [HealthProfileController::class, 'index']);
        Route::post('/health', [HealthProfileController::class, 'store'])->name('profile.health.store');
        Route::put('/health', [HealthProfileController::class, 'update']);
        Route::delete('/health', [HealthProfileController::class, 'destroy']);

        // User Recipe routes
        Route::get('/recipes/favorites', [RecipeController::class, 'getFavoriteRecipes']);
        Route::post('/recipes/favorites/add', [RecipeController::class, 'addFavoriteRecipe']);
        Route::post('/recipes/favorites/remove', [RecipeController::class, 'removeFavoriteRecipe']);
        Route::apiResource('/recipes', RecipeController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        // Progress routes
        Route::get('/progress/all', [ProgressController::class, 'index']);
        Route::get('/progress', [ProgressController::class, 'getProgress']);
        Route::put('/progress', [ProgressController::class, 'updateProgress']);
        Route::get('/progress/analyze', [ProgressController::class, 'analyzeProgress']);
    });

    // Meal Plan API routes
    Route::prefix('meal-plans')->controller(MealPlanController::class)->group(function () {
        Route::get('/recommended', 'getRecommendedMeals');
    });


    // Recipe API routes
    Route::prefix('recipes')->controller(RecipeController::class)->group(function () {
        Route::get('/random', 'randomRecipes');
        Route::get('/recommended', 'getRecommendedRecipes');
        Route::get('/findByIngredients', 'findByIngredients');
        Route::get('/autocomplete', 'autocompleteRecipes');
        Route::get('/{id}', 'getRecipe');
        Route::get('/{id}/similar', 'simillarRecipes');
    });
});


// Public routes
Route::prefix('/public')->controller(PublicController::class)->group(function () {
    Route::get('/allergies', 'allergies');
    Route::get('/medical-conditions', 'medicalConditions');
    Route::get('/activity-levels', 'activityLevels');
    Route::get('/cuisines', 'cuisines');
    Route::get('/dishes', 'dishes');
    Route::get('/dietary-preferences', 'dietaryPreferences');
});


// Guest routes
Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);


    Route::prefix('/profile')->group(function () {
        // Info Profile routes
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
    });

});