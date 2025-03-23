<?php

namespace App\Http\Requests;

use App\Enums\CuisinesEnum;
use App\Enums\DishesEnum;
use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'summary' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'servings' => 'nullable|integer',
            'ready_in_minutes' => 'nullable|integer',
            'source_url' => 'nullable|string',
            'cheap' => 'nullable|boolean',
            'vegetarian' => 'nullable|boolean',
            'vegan' => 'nullable|boolean',
            'very_healthy' => 'nullable|boolean',
            'health_score' => 'nullable|integer',
            'cuisines' => 'nullable|array',
            'cuisines.*' => 'in:' . implode(',', CuisinesEnum::values()),
            'dish_types' => 'nullable|array',
            'dish_types.*' => 'in:' . implode(',', DishesEnum::values()),
            'diets' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'instructions' => 'nullable|string',
            'calories' => 'nullable|numeric',
            'protein' => 'nullable|numeric',
            'carbs' => 'nullable|numeric',
            'fat' => 'nullable|numeric',
            'is_favorite' => 'nullable|boolean'
        ];
    }
}
