<?php

namespace App\Http\Controllers\API;

use App\Enums\ActivityLevelsEnum;
use App\Enums\CuisinesEnum;
use App\Enums\DietaryPreferencesEnum;
use App\Enums\DishesEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllergyResource;
use App\Http\Resources\MedicalConditionResource;
use App\Models\Allergy;
use App\Models\MedicalCondition;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    public function allergies(): JsonResponse
    {
        return $this->response(AllergyResource::collection(Allergy::all()), 'Allergies retrieved successfully');
    }

    public function medicalConditions(): JsonResponse
    {
        return $this->response(MedicalConditionResource::collection(MedicalCondition::all()), 'Medical conditions retrieved successfully');
    }

    public function activityLevels(): JsonResponse
    {
        return $this->response(ActivityLevelsEnum::getOptions(), 'Activity levels retrieved successfully');
    }

    public function cuisines(): JsonResponse
    {
        return $this->response(CuisinesEnum::values(), 'Cuisines retrieved successfully');
    }

    public function dishes(): JsonResponse
    {
        return $this->response(DishesEnum::values(), 'Dishes retrieved successfully');
    }

    public function dietaryPreferences(): JsonResponse
    {
        return $this->response(DietaryPreferencesEnum::values(), 'Dietary preferences retrieved successfully');
    }
}
