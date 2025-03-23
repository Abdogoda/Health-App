<?php

namespace App\Services;

use App\Helpers\HealthHelper;
use App\Helpers\RecipeFormatter;

class RecipesService extends ApiService
{
  private $user;

  public function __construct()
  {
    $this->user = auth()->user();
  }

  public function getRecipesForUser(int $count, int $offset, string $cuisine = null, string $type = null, string $search = null, bool $applyForMe = true)
  {
    $userData = $this->user->profile;
    $medicalRestrictions = [];
    $allergiesRestrictions = [];

    $apiParams = [
      'number' => $count,
      'offset' => $offset,
      'addRecipeNutrition' => "true",
    ];

    if ($applyForMe) {
      $apiParams['maxCalories'] = $userData->daily_caloric_target;
      $apiParams['maxCarbs'] = $userData->carbs_target;
      $apiParams['maxProtein'] = $userData->protein_target;
      $apiParams['maxFat'] = $userData->fat_target;
      $apiParams['diet'] = $userData->dietary_preference ?? '';

      $medicalRestrictions = HealthHelper::getMedicalRestrictions($this->user);
      $allergiesRestrictions = HealthHelper::getAllergyRestrictions($this->user);
    }

    if ($type)
      $apiParams['type'] = $type;
    if ($search)
      $apiParams['query'] = $search;
    if ($cuisine)
      $apiParams['cuisine'] = $cuisine;

    $queryParams = array_merge($apiParams, $medicalRestrictions, $allergiesRestrictions);

    $recipes = $this->sendRequest("https://api.spoonacular.com/recipes/complexSearch", $queryParams);
    // dd($recipes);

    return RecipeFormatter::recipesFormatter($recipes);
  }

  public function getRecipeById(int $id)
  {
    $recipe = $this->sendRequest("https://api.spoonacular.com/recipes/{$id}/information", [
      'includeNutrition' => "true",
    ]);
    if (!$recipe || !isset($recipe['id'])) {
      throw new \Exception('Recipe not found');
    }

    return RecipeFormatter::formatRecipeInformation($recipe);
  }

  public function getSimilarRecipes(int $id, int $count)
  {
    return $this->sendRequest("https://api.spoonacular.com/recipes/{$id}/similar", ['number' => $count]);
  }

  public function getRandomRecipes(int $count)
  {
    return $this->sendRequest("https://api.spoonacular.com/recipes/random", ['number' => $count]);
  }

  public function findByIngredients(string $ingredients, int $count)
  {
    return $this->sendRequest("https://api.spoonacular.com/recipes/findByIngredients", [
      'ingredients' => $ingredients,
      'number' => $count,
      'ranking' => 1,
      'ignorePantry' => false
    ]);
  }

  public function getInformationBulk(array $ids)
  {
    return $this->sendRequest("https://api.spoonacular.com/recipes/informationBulk", ['ids' => implode(',', $ids)]);
  }

  public function autocompleteRecipes(string $query, int $number)
  {
    return $this->sendRequest("https://api.spoonacular.com/recipes/autocomplete", ['query' => $query, 'number' => $number]);
  }
}
