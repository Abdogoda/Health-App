<?php

namespace App\Http\Requests\HealthProfile;

use App\Enums\ActivityLevelsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateHealthProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'birth_date' => 'date',
            'height' => 'numeric',
            'weight' => 'numeric',
            'gender' => 'in:male,female',
            'health_goal' => 'in:gain,loss,stable',
            'activity_level' => [new Enum(ActivityLevelsEnum::class)],
            'dietary_preference' => 'nullable|string',

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
