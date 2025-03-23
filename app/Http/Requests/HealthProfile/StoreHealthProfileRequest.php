<?php

namespace App\Http\Requests\HealthProfile;

use App\Enums\ActivityLevelsEnum;
use App\Enums\DietaryPreferencesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreHealthProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'birth_date' => 'required|date',
            'height' => 'required|numeric|min:50|max:300',
            'weight' => 'required|numeric|min:20|max:500',
            'gender' => 'required|in:male,female',
            'health_goal' => 'required|in:gain,loss,stable',
            'activity_level' => ['required', new Enum(ActivityLevelsEnum::class)],
            'dietary_preference' => ['nullable', new Enum(DietaryPreferencesEnum::class)],

            'medical_conditions' => 'nullable|array',
            'medical_conditions.*' => 'exists:medical_conditions,id',
            'family_medical_history' => 'nullable|array',
            'family_medical_history.*' => 'exists:family_medical_histories,id',
            'allergies' => 'nullable|array',
            'allergies.*' => 'exists:allergies,id',

            'receive_daily_report' => 'boolean',
        ];
    }
}
