<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'image' => $this->image,
            'servings' => $this->servings,
            'ready_in_minutes' => $this->ready_in_minutes,
            'source_url' => $this->source_url,

            'cheap' => $this->cheap,
            'vegetarian' => $this->vegetarian,
            'vegan' => $this->vegan,
            'very_healthy' => $this->very_healthy,
            'health_score' => $this->health_score,

            'cuisines' => $this->cuisines,
            'dish_types' => $this->dish_types,
            'diets' => $this->diets,
            'ingredients' => $this->ingredients,

            'instructions' => $this->instructions,

            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,

            'is_favorite' => $this->is_favorite,
            'user_id' => $this->user_id,

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
