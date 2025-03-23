<?php

namespace Database\Seeders;

use App\Enums\MedicalConditionsEnum;
use App\Models\FamilyMedicalHistory;
use App\Models\MedicalCondition;
use Illuminate\Database\Seeder;

class MedicalConditionsSeeder extends Seeder
{
    public function run(): void
    {
        $medicalConditions = collect(MedicalConditionsEnum::cases())->map(fn($case) => [
            'name' => $case->value
        ])->toArray();

        MedicalCondition::insertOrIgnore($medicalConditions);
        FamilyMedicalHistory::insertOrIgnore($medicalConditions);
    }
}
