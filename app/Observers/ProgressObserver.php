<?php

namespace App\Observers;

use App\Models\Progress;

class ProgressObserver
{
    public function updated(Progress $progress): void
    {
        $userProfile = $progress->user->profile;

        if ($progress->isDirty('weight') && $progress->weight !== null) {
            $userProfile->weight = $progress->weight;
            $userProfile->save();
        }

        if ($progress->isDirty('calories_consumed')) {
            $note = '';

            if ($progress->calories_consumed > $userProfile->daily_caloric_target) {
                $note = " (⚠️ Exceeded daily caloric target)";
            } elseif ($progress->calories_consumed < $userProfile->daily_caloric_target) {
                $note = " (ℹ️ Below daily caloric target)";
            } else {
                $note = " (✅ Met daily caloric target)";
            }

            if (!str_contains($progress->notes, $note)) {
                $progress->updateQuietly(['notes' => trim($progress->notes . $note)]);
            }
        }
    }
}
