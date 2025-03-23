<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'birth_date' => $this->birth_date,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => $this->gender,
            'health_goal' => $this->health_goal,
            'activity_level' => $this->activity_level,
            'dietary_preference' => $this->dietary_preference,

            'daily_caloric_target' => $this->daily_caloric_target,
            'protein_target' => $this->protein_target,
            'carbs_target' => $this->carbs_target,
            'fat_target' => $this->fat_target,

            'medical_conditions' => MedicalConditionResource::collection($this->user->medicalConditions),
            'family_medical_histories' => MedicalConditionResource::collection($this->user->familyMedicalHistories),
            'allergies' => AllergyResource::collection($this->user->allergies),

            'receive_daily_report' => $this->receive_daily_report,

            'dietary_instructions' => $this->dietary_instructions
        ];
    }
}
