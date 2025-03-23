<?php

namespace App\Services;

use App\Models\User;

class ProgressService
{
  public function createProgress(User $user, array $data): void
  {
    $progress = $user->progress()->create([
      'date' => $data['date'],
      'weight' => $data['weight'] ?? $user->weight,
      'calories_consumed' => $data['calories_consumed'] ?? 0,
      'calories_burned' => $data['calories_burned'] ?? 0,
      'protein' => $data['protein'] ?? 0,
      'carbs' => $data['carbs'] ?? 0,
      'fats' => $data['fats'] ?? 0,
      'notes' => $data['notes'] ?? '',
    ]);
    return $progress;
  }
}