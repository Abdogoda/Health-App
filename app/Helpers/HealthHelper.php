<?php

namespace App\Helpers;

use App\Enums\AllergiesEnum;
use App\Enums\DishesEnum;
use App\Enums\MedicalConditionsEnum;
use App\Models\Progress;
use App\Models\User;
use App\Services\RecipesService;

class HealthHelper
{

  /**
   * Calculate daily caloric needs based on height, weight, gender, age, activity level, and goal.
   */
  public static function calculateCalories($height, $weight, $gender, $goal, $age, $activityLevel = 1.2): int
  {
    $bmr = ($gender === 'male')
      ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
      : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;

    // Adjust for activity level (TDEE)
    $tdee = (int) ($bmr * $activityLevel);

    return match ($goal) {
      'gain' => $tdee + 500,  // Surplus
      'loss' => $tdee - 500,  // Deficit
      default => $tdee,       // Maintenance
    };
  }

  /**
   * Calculate macronutrient targets (protein, carbs, fat) based on daily caloric needs and goal.
   */
  public static function calculateMacros($dailyCaloricTarget, string $goal): array
  {
    $macros = match ($goal) {
      'gain' => ['protein' => 0.3, 'carbs' => 0.45, 'fat' => 0.25],
      'loss' => ['protein' => 0.4, 'carbs' => 0.3, 'fat' => 0.3],
      default => ['protein' => 0.3, 'carbs' => 0.4, 'fat' => 0.3],
    };

    return [
      'protein' => (int) (($dailyCaloricTarget * $macros['protein']) / 4),
      'carbs' => (int) (($dailyCaloricTarget * $macros['carbs']) / 4),
      'fat' => (int) (($dailyCaloricTarget * $macros['fat']) / 9),
    ];
  }

  /**
   * Apply allergy restrictions to the query parameters.
   */
  public static function getAllergyRestrictions($user): array
  {
    $allergies = $user->allergies->pluck('name')->toArray();

    $excludeIngredients = [];
    if (in_array(AllergiesEnum::PEANUTS->value, $allergies)) {
      $excludeIngredients[] = 'peanuts';
    }

    if (in_array(AllergiesEnum::SEAFOOD->value, $allergies)) {
      $excludeIngredients = array_merge($excludeIngredients, ['fish', 'shrimp', 'crab', 'lobster']);
    }

    if (in_array(AllergiesEnum::MILK->value, $allergies)) {
      $excludeIngredients = array_merge($excludeIngredients, ['milk', 'cheese', 'butter', 'yogurt']);
    }

    if (in_array(AllergiesEnum::EGG->value, $allergies)) {
      $excludeIngredients[] = 'egg';
    }

    if (in_array(AllergiesEnum::WHEAT->value, $allergies)) {
      $excludeIngredients = array_merge($excludeIngredients, ['wheat', 'gluten']);
    }

    if (in_array(AllergiesEnum::SOY->value, $allergies)) {
      $excludeIngredients = array_merge($excludeIngredients, ['soy', 'soybeans', 'tofu']);
    }

    if (in_array(AllergiesEnum::SESAME->value, $allergies)) {
      $excludeIngredients[] = 'sesame';
    }

    return ['excludeIngredients' => implode(',', $excludeIngredients)];
  }


  /**
   * Apply medical condition restrictions to the query parameters.
   */
  public static function getMedicalRestrictions($user): array
  {
    $medicalConditions = $user->medicalConditions->pluck('name')->toArray();
    $familyHistory = $user->familyMedicalHistories->pluck('name')->toArray();

    $allConditions = array_unique(array_merge($medicalConditions, $familyHistory));

    $filters = [];

    foreach ($allConditions as $condition) {
      switch ($condition) {
        case MedicalConditionsEnum::DIABETES->value:
          $filters['maxSugar'] = 5; // Limit sugar to 5g per serving
          break;
        case MedicalConditionsEnum::HYPERTENSION->value:
          $filters['maxSodium'] = 500; // Limit sodium to 500mg
          break;
        case MedicalConditionsEnum::HEART_DISEASE->value:
          $filters['maxSaturatedFat'] = 5; // Limit saturated fat
          break;
        case MedicalConditionsEnum::ASTHMA->value:
          $filters['excludeIngredients'] = 'dairy,nuts'; // Common asthma triggers
          break;
        case MedicalConditionsEnum::KIDNEY_DISEASE->value:
          $filters['maxPotassium'] = 2000; // Limit potassium
          $filters['maxPhosphorus'] = 800; // Limit phosphorus
          break;
        case MedicalConditionsEnum::LIVER_DISEASE->value:
          $filters['maxProtein'] = 70; // Limit protein intake
          break;
        case MedicalConditionsEnum::ANEMIA->value:
          $filters['minIron'] = 10; // Ensure sufficient iron intake
          break;
        case MedicalConditionsEnum::OBESITY->value:
          $filters['maxCalories'] = 500; // Keep meals under 500 calories
          $filters['minProtein'] = 20; // Ensure high-protein diet
          break;
        case MedicalConditionsEnum::THYROID_DISORDER->value:
          $filters['excludeIngredients'] = 'soy,cruciferous vegetables'; // Common thyroid disruptors
          break;
        case MedicalConditionsEnum::HEPATITIS_C->value:
          $filters['maxFat'] = 50; // Reduce fat intake
          break;
        case MedicalConditionsEnum::CANCER->value:
          $filters['maxProcessedMeat'] = 0; // Avoid processed meats
          break;
        case MedicalConditionsEnum::OSTEOPOROSIS->value:
          $filters['minCalcium'] = 1000; // Ensure calcium intake
          break;
        case MedicalConditionsEnum::STROKE->value:
          $filters['maxCholesterol'] = 200; // Limit cholesterol
          break;
        case MedicalConditionsEnum::GASTRIC_ULCER->value:
          $filters['excludeIngredients'] = 'spicy foods,caffeine,alcohol'; // Common ulcer triggers
          break;
        case MedicalConditionsEnum::MIGRAINE->value:
          $filters['excludeIngredients'] = 'chocolate,caffeine,aged cheese'; // Common migraine triggers
          break;
        case MedicalConditionsEnum::DEPRESSION->value:
          $filters['minOmega3'] = 500; // Ensure omega-3 intake
          break;
        case MedicalConditionsEnum::EPILEPSY->value:
          $filters['diet'] = 'keto'; // Keto diet may help epilepsy
          break;
        case MedicalConditionsEnum::AUTOIMMUNE_DISEASE->value:
          $filters['excludeIngredients'] = 'gluten,dairy'; // Common autoimmune triggers
          break;
      }
    }

    return $filters;
  }

  public static function getMedicalConditionInstructions($user): array
  {
    $medicalConditions = $user->medicalConditions->pluck('name')->toArray();
    $familyHistory = $user->familyMedicalHistories->pluck('name')->toArray();

    $allConditions = array_unique(array_merge($medicalConditions, $familyHistory));

    $instructions = [];

    foreach ($allConditions as $condition) {
      switch ($condition) {
        case MedicalConditionsEnum::DIABETES->value:
          $instructions[] = "🩸 For diabetes, avoid sugary foods, processed carbohydrates, and high-GI fruits. Opt for fiber-rich vegetables, whole grains, and lean proteins.";
          break;
        case MedicalConditionsEnum::HYPERTENSION->value:
          $instructions[] = "🫀 To manage hypertension, reduce sodium intake by avoiding processed foods. Eat potassium-rich foods like bananas, oranges, and spinach.";
          break;
        case MedicalConditionsEnum::HEART_DISEASE->value:
          $instructions[] = "❤️ For heart health, focus on omega-3-rich foods (salmon, flaxseeds) and reduce saturated fat from red meat and fried foods.";
          break;
        case MedicalConditionsEnum::ASTHMA->value:
          $instructions[] = "🌬️ Asthma patients should avoid dairy, artificial additives, and sulfites (found in wine & dried fruits). Include vitamin C-rich foods.";
          break;
        case MedicalConditionsEnum::KIDNEY_DISEASE->value:
          $instructions[] = "🦾 For kidney health, limit potassium (bananas, potatoes) and phosphorus (processed foods). Stay hydrated and eat lean protein.";
          break;
        case MedicalConditionsEnum::LIVER_DISEASE->value:
          $instructions[] = "🧬 For liver support, avoid alcohol, fried foods, and excess protein. Include antioxidant-rich foods like green tea and berries.";
          break;
        case MedicalConditionsEnum::ANEMIA->value:
          $instructions[] = "🩸 If anemic, increase iron-rich foods (red meat, spinach, lentils). Combine iron sources with vitamin C (citrus fruits) for better absorption.";
          break;
        case MedicalConditionsEnum::OBESITY->value:
          $instructions[] = "⚖️ For weight control, reduce processed foods, control portion sizes, and prioritize high-fiber foods like oats, vegetables, and legumes.";
          break;
        case MedicalConditionsEnum::THYROID_DISORDER->value:
          $instructions[] = "🦋 For thyroid health, limit soy, cruciferous vegetables (cabbage, broccoli), and processed foods. Ensure enough iodine (seafood, eggs).";
          break;
        case MedicalConditionsEnum::HEPATITIS_C->value:
          $instructions[] = "🩺 If you have Hepatitis C, reduce fatty foods and increase anti-inflammatory foods like turmeric, garlic, and leafy greens.";
          break;
        case MedicalConditionsEnum::CANCER->value:
          $instructions[] = "🎗️ Cancer patients should avoid processed meats, alcohol, and high-sugar foods. Eat antioxidant-rich foods like berries, nuts, and cruciferous vegetables.";
          break;
        case MedicalConditionsEnum::OSTEOPOROSIS->value:
          $instructions[] = "🦴 Strengthen bones with calcium-rich foods (dairy, leafy greens) and vitamin D sources (salmon, eggs). Limit soft drinks and caffeine.";
          break;
        case MedicalConditionsEnum::STROKE->value:
          $instructions[] = "🧠 Reduce stroke risk by limiting salt and saturated fat. Increase fiber intake from whole grains, beans, and fresh fruits.";
          break;
        case MedicalConditionsEnum::GASTRIC_ULCER->value:
          $instructions[] = "🫛 Avoid acidic foods (citrus, tomatoes), caffeine, and spicy foods. Eat probiotic-rich foods like yogurt to support digestion.";
          break;
        case MedicalConditionsEnum::MIGRAINE->value:
          $instructions[] = "💆‍♂️ Avoid migraine triggers like chocolate, caffeine, and aged cheese. Stay hydrated and consume magnesium-rich foods (nuts, seeds, spinach).";
          break;
        case MedicalConditionsEnum::DEPRESSION->value:
          $instructions[] = "😊 Improve mood with omega-3 fatty acids (salmon, walnuts) and tryptophan-rich foods (turkey, bananas, dark chocolate). Avoid alcohol and processed sugars.";
          break;
        case MedicalConditionsEnum::EPILEPSY->value:
          $instructions[] = "⚡ The keto diet (high fat, low carb) may help epilepsy. Avoid artificial sweeteners, processed foods, and refined sugar.";
          break;
        case MedicalConditionsEnum::AUTOIMMUNE_DISEASE->value:
          $instructions[] = "🛡️ Avoid gluten, dairy, and processed foods. Include anti-inflammatory foods like turmeric, ginger, and leafy greens.";
          break;
      }
    }

    return $instructions;
  }

  /**
   * Get the nutrient amount for a given nutrient name.
   */
  public static function getNutrient(array $nutrients, string $nutrientName)
  {
    foreach ($nutrients as $nutrient) {
      if ($nutrient['name'] === $nutrientName) {
        return $nutrient['amount'];
      }
    }
    return null;
  }

  /**
   * Get the daily meal plan for a user based on their progress and health profile.
   */
  public static function getDailyMealPlan(RecipesService $recipesService, User $user, Progress $progress)
  {
    $userData = $user->profile;
    $dailyCalories = $userData->daily_caloric_target;

    // Calculate remaining nutrients
    $remainingCalories = $dailyCalories - $progress->calories_consumed + $progress->calories_burned;
    $remainingProtein = max(0, $userData->protein_target - $progress->protein_consumed);
    $remainingCarbs = max(0, $userData->carbs_target - $progress->carbs_consumed);
    $remainingFat = max(0, $userData->fat_target - $progress->fat_consumed);

    $meals = [
      'breakfast' => $recipesService->getRecipesForUser(3, 0, null, DishesEnum::BREAKFAST->value, applyForMe: false),
      'lunch' => $recipesService->getRecipesForUser(3, 0, null, DishesEnum::MAIN_COURSE->value, applyForMe: false),
      'dinner' => $recipesService->getRecipesForUser(3, 0, null, DishesEnum::MAIN_COURSE->value, applyForMe: false),
      'snacks' => $recipesService->getRecipesForUser(3, 0, null, DishesEnum::SNACK->value, applyForMe: false),
    ];

    return [
      'remaining_calories' => $remainingCalories,
      'remaining_protein' => $remainingProtein,
      'remaining_carbs' => $remainingCarbs,
      'remaining_fats' => $remainingFat,
      'meals' => $meals
    ];
  }
}
