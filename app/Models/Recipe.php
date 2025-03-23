<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'title',
        'summary',
        'image',
        'servings',
        'ready_in_minutes',
        'source_url',

        'cheap',
        'vegetarian',
        'vegan',
        'very_healthy',
        'health_score',

        'cuisines',
        'dish_types',
        'diets',
        'ingredients',
        'instructions',

        'calories',
        'protein',
        'carbs',
        'fat',

        'is_favorite',
        'user_id'
    ];

    protected $casts = [
        'cuisines' => 'array',
        'dish_types' => 'array',
        'diets' => 'array',
        'ingredients' => 'array',
    ];

    public function getImageAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) ? $value : ($value ? asset('storage/' . $value) : null);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
