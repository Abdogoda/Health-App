<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userProfile = $this->user->profile;

        return [
            'date' => Carbon::parse($this->date)->format('l, F j, Y'),
            'weight' => $this->weight ?? $userProfile->weight,
            'calories' => [
                'consumed' => [
                    'value' => $this->calories_consumed,
                    'target' => $userProfile->daily_caloric_target,
                    'status' => $this->calories_consumed > $userProfile->daily_caloric_target ? '⚠️ Over target' : '✅ Within target'
                ],
                'burned' => [
                    'value' => $this->calories_burned,
                    'recommended' => round($userProfile->daily_caloric_target * 0.15), // Suggested 15% of target as burned calories
                    'status' => $this->calories_burned >= ($userProfile->daily_caloric_target * 0.15) ? '✅ Good' : '⚠️ Low activity'
                ]
            ],
            'macronutrients' => [
                'protein' => "{$this->protein}g / {$userProfile->protein_target}g",
                'carbs' => "{$this->carbs}g / {$userProfile->carbs_target}g",
                'fats' => "{$this->fats}g / {$userProfile->fat_target}g"
            ],
            'notes' => $this->notes ?? 'No notes available',
        ];
    }
}
