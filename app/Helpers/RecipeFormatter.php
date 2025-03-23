<?php
namespace App\Helpers;

class RecipeFormatter
{

  /**
   * Format the recipes data to a more readable format.
   */
  public static function recipesFormatter(array $response)
  {
    $formattedRecipes = [
      'totalResults' => $response['totalResults'] ?? 0,
      'offset' => $response['offset'] ?? 0,
      'count' => $response['number'] ?? 0,
      'recipes' => array_map(function ($recipe) {
        return [
          'id' => $recipe['id'],
          'title' => $recipe['title'],
          'image' => $recipe['image'],
          'calories' => (HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Calories') ?? 0) . ' kcal',
          'carbs' => (HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Carbohydrates') ?? 0) . ' g',
          'protein' => (HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Protein') ?? 0) . ' g',
          'fat' => (HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Fat') ?? 0) . ' g',
        ];
      }, $response['results'])
    ];
    return $formattedRecipes;
  }


  /**
   * Format the recipe data to a more readable format.
   */

  public static function formatRecipeInformation(array $recipe)
  {
    $formattedRecipe = [
      'id' => $recipe['id'] ?? null,
      'title' => $recipe['title'] ?? 'Unknown Recipe',
      'image' => $recipe['image'] ?? 'https://via.placeholder.com/500',
      'summary' => strip_tags($recipe['summary'] ?? 'No summary available.'),
      'servings' => $recipe['servings'] ?? 1,
      'cheap' => $recipe['cheap'] ?? false,
      'vegetarian' => $recipe['vegetarian'] ?? false,
      'vegan' => $recipe['vegan'] ?? false,
      'very_healthy' => $recipe['veryHealthy'] ?? false,
      'health_score' => $recipe['healthScore'] ?? 0,
      'ready_in_minutes' => $recipe['readyInMinutes'] ?? 30,
      'source_url' => $recipe['sourceUrl'] ?? '',
      'cuisines' => $recipe['cuisines'] ?? ['Unknown Cuisine'],
      'dish_types' => $recipe['dishTypes'] ?? ['Unknown Dish Type'],
      'diets' => $recipe['diets'] ?? ['Unknown Diet'],
      'ingredients' => isset($recipe['extendedIngredients'])
        ? array_map(fn($ingredient) => [
          'name' => $ingredient['name'] ?? 'Unknown Ingredient',
          'amount' => $ingredient['amount'] ?? 0,
          'unit' => $ingredient['unit'] ?? '',
        ], $recipe['extendedIngredients'])
        : [],
      'instructions' => $recipe['instructions'] ?? 'No instructions available.',
    ];

    if (isset($recipe['nutrition']['nutrients'])) {
      $formattedRecipe['calories'] = HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Calories');
      $formattedRecipe['protein'] = HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Protein');
      $formattedRecipe['carbs'] = HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Carbohydrates');
      $formattedRecipe['fat'] = HealthHelper::getNutrient($recipe['nutrition']['nutrients'], 'Fat');
    }

    return $formattedRecipe;
  }
}