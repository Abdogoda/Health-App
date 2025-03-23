<?php

namespace App\Http\Controllers\API;

use App\Helpers\HealthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\HealthProfile\StoreHealthProfileRequest;
use App\Http\Requests\HealthProfile\UpdateHealthProfileRequest;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserResource;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HealthProfileController extends Controller
{
    public function __construct()
    {
        if (!Auth::user()->profile && !request()->routeIs('profile.health.store')) {
            abort(404, 'User profile not found. Please create a user profile first');
        }
    }

    public function index()
    {
        $healthProfile = Auth::user()->profile;
        return $this->response(UserProfileResource::make($healthProfile));
    }

    public function store(StoreHealthProfileRequest $request)
    {
        $user = Auth::user();

        if ($user->profile) {
            return $this->response(null, 'User profile already exists', 400);
        }

        $validated = $request->validated();

        $age = Carbon::parse($validated['birth_date'])->age;

        $dailyCaloricTarget = HealthHelper::calculateCalories($validated['height'], $validated['weight'], $validated['gender'], $validated['health_goal'], $age, $validated['activity_level']);
        $macros = HealthHelper::calculateMacros($dailyCaloricTarget, $validated['health_goal']);

        $userProfile = $user->profile()->create([
            'birth_date' => $validated['birth_date'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'gender' => $validated['gender'],
            'health_goal' => $validated['health_goal'],
            'activity_level' => $validated['activity_level'],
            'dietary_preference' => $validated['dietary_preference'] ?? null,
            'receive_daily_report' => $validated['receive_daily_report'] ?? false,
            'daily_caloric_target' => $dailyCaloricTarget,
            'protein_target' => $macros['protein'],
            'carbs_target' => $macros['carbs'],
            'fat_target' => $macros['fat'],
        ]);

        $user->allergies()->sync($validated['allergies'] ?? []);

        if (isset($validated['medical_conditions']) || isset($validated['family_medical_history'])) {
            $user->familyMedicalHistories()->sync($validated['family_medical_history']);
            $user->medicalConditions()->sync($validated['medical_conditions']);

            $userProfile->dietary_instructions = HealthHelper::getMedicalConditionInstructions($user);
            $userProfile->save();
        }

        return $this->response(message: 'User profile created successfully', status: 201);
    }

    public function update(UpdateHealthProfileRequest $request)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();
        $validated = $request->validated();

        $profile->update([
            'birth_date' => $validated['birth_date'] ?? $profile->birth_date,
            'height' => $validated['height'] ?? $profile->height,
            'weight' => $validated['weight'] ?? $profile->weight,
            'gender' => $validated['gender'] ?? $profile->gender,
            'health_goal' => $validated['health_goal'] ?? $profile->health_goal,
            'receive_daily_report' => $validated['receive_daily_report'] ?? $profile->receive_daily_report,
            'dietary_preference' => $validated['dietary_preference'] ?? $profile->dietary_preference,
        ]);

        // Sync relationships
        if (isset($validated['medical_conditions'])) {
            $user->medicalConditions()->sync($validated['medical_conditions']);
        }

        if (isset($validated['family_medical_history'])) {
            $user->familyMedicalHistories()->sync($validated['family_medical_history']);
        }

        if (isset($validated['allergies'])) {
            $user->allergies()->sync($validated['allergies']);
        }

        $user->profile->dietary_instructions = HealthHelper::getMedicalConditionInstructions($user);
        $user->profile->save();

        return $this->response(UserResource::make($user), 'User profile updated successfully');
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->profile->delete();
        $user->medicalConditions()->detach();
        $user->familyMedicalHistories()->detach();
        $user->allergies()->detach();
        $user->progress()->delete();

        return $this->response(message: 'User profile deleted successfully');
    }
}
