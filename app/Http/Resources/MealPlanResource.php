<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'description' => $this->description,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->groupBy('date')->map(function ($items, $date) {
                    return [
                        'date' => $date,
                        'meals' => $items->groupBy('meal_type')->map(function ($items, $mealType) {
                            return [
                                'meal_type' => $mealType,
                                'recipes' => $items->map(function ($item) {
                                    return [
                                        'recipe' => new RecipeResource($item->recipe),
                                        'servings' => $item->servings,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values();
            }),
            'user_id' => $this->user_id
        ];
    }
}
