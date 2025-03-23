<?php

namespace App\Observers;

use App\Helpers\HealthHelper;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserProfileObserver
{
    public function updated(UserProfile $userProfile): void
    {

        Log::info('UserProfileObserver triggered for user: ' . $userProfile->id);
        if ($userProfile->isDirty(['height', 'weight', 'health_goal', 'birth_date'])) {
            $age = Carbon::parse($userProfile->birth_date)->age;


            $dailyCaloricTarget = HealthHelper::calculateCalories(
                $userProfile->height,
                $userProfile->weight,
                $userProfile->gender,
                $userProfile->health_goal,
                $age
            );

            $macros = HealthHelper::calculateMacros($dailyCaloricTarget, $userProfile->health_goal);

            $userProfile->updateQuietly([
                'daily_caloric_target' => $dailyCaloricTarget,
                'protein_target' => $macros['protein'],
                'carbs_target' => $macros['carbs'],
                'fat_target' => $macros['fat']
            ]);
        }
    }

}
