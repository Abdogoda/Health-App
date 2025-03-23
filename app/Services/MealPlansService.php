<?php

namespace App\Services;

use App\Helpers\HealthHelper;

class MealPlansService extends ApiService
{
  private $user;
  private $recipesService;

  public function __construct(RecipesService $recipesService)
  {
    $this->user = auth()->user();
    $this->recipesService = $recipesService;
  }


  public function generatePlan(string $timeFrame = 'day', int $targetCalories = null)
  {
    $apiParams = [
      'timeFrame' => $timeFrame,
      'targetCalories' => $targetCalories ?? $this->user->profile->daily_caloric_target,
      'diet' => $this->user->profile->dietary_preference,
    ];

    $medicalRestrictions = HealthHelper::getMedicalRestrictions($this->user);
    $allergiesRestrictions = HealthHelper::getAllergyRestrictions($this->user);

    $params = array_merge($apiParams, $medicalRestrictions, $allergiesRestrictions);

    $meals = $this->sendRequest("https://api.spoonacular.com/mealplanner/generate", $params);
    $mealIds = array_map(function ($meal) {
      return $meal['id'];
    }, $meals['meals']);

    $nutrition = $meals['nutrients'];

    $mealRecipes = $this->recipesService->getInformationBulk($mealIds);

    return [
      'meals' => $mealRecipes,
      'nutrition' => $nutrition,
    ];
  }
}
