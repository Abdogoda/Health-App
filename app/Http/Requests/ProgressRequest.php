<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'weight' => 'nullable|numeric',
            'calories_consumed' => 'nullable|integer',
            'calories_burned' => 'nullable|integer',
            'protein' => 'nullable|integer',
            'carbs' => 'nullable|integer',
            'fats' => 'nullable|integer',
            'notes' => 'nullable|string',
        ];
    }
}
